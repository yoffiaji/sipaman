<?php

namespace App\Imports;

use App\Models\PirtCommitmentStatus;
use App\Models\Produk;
use App\Models\Role;
use App\Models\User;
use App\Models\VerifikasiProduk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import file "Status Pemenuhan Komitmen".
 *
 * Struktur file:
 * A No, B No SPPIRT, C Provinsi, D Kab/Kota, E Nama Pelaku Usaha,
 * F Alamat Usaha, G Phone, H Terdaftar, I NIB, J Verifikasi Produk,
 * K Verifikasi Label, L PKP, M CPPOB, N Status Pemenuhan Komitmen.
 *
 * Side-effect penting:
 * - Produk dengan 4 kolom centang penuh → is_verified = true
 * - Untuk produk yang baru saja jadi verified DAN punya NIB,
 *   sistem otomatis membuat akun User pelaku usaha (kalau belum ada).
 * - Akun yang baru dibuat: password = null. Admin harus set password
 *   dulu sebelum pelaku usaha bisa login.
 */
class PirtCommitmentStatusImport implements SkipsEmptyRows, ToCollection, WithStartRow
{
    use Importable;

    private int $berhasil = 0;
    private int $gagal = 0;
    private int $userBaruDibuat = 0;
    private array $failureDetails = [];

    /** @var int|null ID admin/super_admin yang menjalankan import — disuntik dari controller */
    private ?int $verifikatorId;

    /** Cache role 'user' supaya tidak query berulang */
    private ?int $userRoleId = null;

    public function __construct(?int $verifikatorId = null)
    {
        $this->verifikatorId = $verifikatorId;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $row = $row->toArray();
            $barisExcel = $index + $this->startRow();

            if ($this->isEmptyRow($row)) {
                continue;
            }

            try {
                $noSppirt = $this->cleanString($this->valueAt($row, 1));

                if (! $noSppirt) {
                    $this->addFailure($barisExcel, 'no_sppirt', 'No SPPIRT wajib diisi.', $row);
                    continue;
                }

                $produk = Produk::where('no_sppirt', $noSppirt)->first();

                if (! $produk) {
                    $this->addFailure(
                        $barisExcel,
                        'no_sppirt',
                        "No SPPIRT {$noSppirt} belum ada di data produk. Pastikan file 'Rekap Data PIRT' yang ter-update sudah di-import lebih dulu.",
                        $row
                    );
                    continue;
                }

                $nibDariStatus = $this->cleanString($this->valueAt($row, 8));
                $namaPelakuUsahaStatus = $this->cleanString($this->valueAt($row, 4));
                $alamatUsahaStatus = $this->cleanString($this->valueAt($row, 5));
                $phoneStatus = $this->cleanString($this->valueAt($row, 6));

                $verifikasiProduk = $this->parseBoolean($this->valueAt($row, 9));
                $verifikasiLabel  = $this->parseBoolean($this->valueAt($row, 10));
                $pkp              = $this->parseBoolean($this->valueAt($row, 11));
                $cppob            = $this->parseBoolean($this->valueAt($row, 12));
                $statusKomitmen   = VerifikasiProduk::hitungStatusKomitmen(
                    $verifikasiProduk, $verifikasiLabel, $pkp, $cppob
                );

                PirtCommitmentStatus::updateOrCreate(
                    ['no_sppirt' => $noSppirt],
                    [
                        'produk_id'                 => $produk->id,
                        'provinsi'                  => $this->cleanString($this->valueAt($row, 2)),
                        'kab_kota'                  => $this->cleanString($this->valueAt($row, 3)),
                        'nama_pelaku_usaha'         => $namaPelakuUsahaStatus,
                        'alamat_usaha'              => $alamatUsahaStatus,
                        'phone'                     => $phoneStatus,
                        'tanggal_terdaftar'         => $this->parseTanggal($this->valueAt($row, 7)),
                        'nib'                       => $nibDariStatus,
                        'verifikasi_produk'         => $verifikasiProduk,
                        'verifikasi_label'          => $verifikasiLabel,
                        'pkp'                       => $pkp,
                        'cppob_pemeriksaan_sarana'  => $cppob,
                        'status_pemenuhan_komitmen' => $this->cleanString($this->valueAt($row, 13)),
                    ]
                );

                VerifikasiProduk::updateOrCreate(
                    ['produk_id' => $produk->id],
                    [
                        'user_verifikator_id'      => $this->verifikatorId ?? auth()->id(),
                        'verifikasi_produk'        => $verifikasiProduk,
                        'verifikasi_label'         => $verifikasiLabel,
                        'pkp'                      => $pkp,
                        'cppob_pemeriksaan_sarana' => $cppob,
                        'status_komitmen'          => $statusKomitmen,
                        'catatan'                  => 'Sinkron otomatis dari file Status Pemenuhan Komitmen.',
                    ]
                );

                $tanggalVerifikasi = $statusKomitmen ? now()->toDateString() : null;
                $nibLogin = $produk->nib ?: $nibDariStatus;
                $produkUpdate = [
                    'is_verified'        => $statusKomitmen,
                    'tanggal_verifikasi' => $tanggalVerifikasi,
                    'masa_berlaku_pirt'  => $statusKomitmen ? now()->addYears(5)->toDateString() : null,
                ];

                if (! $produk->nib && $nibDariStatus) {
                    $produkUpdate['nib'] = $nibDariStatus;
                }

                if (! $produk->no_hp && $phoneStatus) {
                    $produkUpdate['no_hp'] = $phoneStatus;
                }

                if (! $produk->alamat && $alamatUsahaStatus) {
                    $produkUpdate['alamat'] = $alamatUsahaStatus;
                }

                // Auto-create user pelaku usaha kalau produk lulus verifikasi
                // dan punya NIB dari Rekap PIRT atau fallback dari file status.
                // Satu NIB = satu user; admin cukup set password, bukan email.
                if ($statusKomitmen && $nibLogin) {
                    $userId = $this->ensureUserForNib(
                        nib: $nibLogin,
                        namaPelakuUsaha: $produk->nama_pelaku_usaha ?: $namaPelakuUsahaStatus,
                        namaBranding: $produk->nama_branding
                    );

                    if ($userId && $produk->user_id !== $userId) {
                        $produkUpdate['user_id'] = $userId;
                    }
                }

                $produk->update($produkUpdate);

                $this->berhasil++;
            } catch (\Throwable $e) {
                $this->addFailure($barisExcel, 'exception', $e->getMessage(), $row);
            }
        }
    }

    public function getBerhasil(): int
    {
        return $this->berhasil;
    }

    public function getGagal(): int
    {
        return $this->gagal;
    }

    public function getUserBaruDibuat(): int
    {
        return $this->userBaruDibuat;
    }

    public function getFailureDetails(): array
    {
        return $this->failureDetails;
    }

    /**
     * Pastikan ada user dengan NIB ini. Kalau belum ada, buat baru
     * dengan password = null (belum bisa login sampai admin set password).
     *
     * Return: user_id, atau null kalau gagal create.
     */
    private function ensureUserForNib(string $nib, ?string $namaPelakuUsaha, ?string $namaBranding): ?int
    {
        $user = User::where('nib', $nib)->first();

        if ($user) {
            return $user->id;
        }

        if ($this->userRoleId === null) {
            $role = Role::where('nama_role', 'user')->first();
            if (! $role) {
                // Role 'user' belum di-seed — return null, biar admin manual nanti
                return null;
            }
            $this->userRoleId = $role->id;
        }

        $user = User::create([
            'nama'        => $namaPelakuUsaha ?: ($namaBranding ?: "Pelaku Usaha {$nib}"),
            'email'       => null,
            'nib'         => $nib,
            'password'    => null,
            'role_id'     => $this->userRoleId,
            'status_akun' => 'aktif',
        ]);

        $this->userBaruDibuat++;

        return $user->id;
    }

    // ── Private helpers ───────────────────────────────────────

    private function valueAt(array $row, int $index): mixed
    {
        return array_key_exists($index, $row) ? $row[$index] : null;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function cleanString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return number_format((float) $value, 0, '', '');
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseBoolean(mixed $value): bool
    {
        $value = $this->cleanString($value);

        if ($value === null) {
            return false;
        }

        $normalized = mb_strtolower($value);

        return in_array($normalized, [
            '1', 'true', 'ya', 'iya', 'y', 'yes', 'ok', 'valid', 'lulus', 'terpenuhi', '✅', '✔', '✓', 'v'
        ], true);
    }

    private function parseTanggal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d');
            }

            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }

            $value = trim((string) $value);

            foreach (['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y'] as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->toDateString();
                } catch (\Throwable) {
                }
            }

            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function addFailure(int $baris, string $kolom, string $message, array $row): void
    {
        $this->gagal++;
        $this->failureDetails[] = [
            'baris'  => $baris,
            'kolom'  => $kolom,
            'errors' => [$message],
            'nilai'  => $row,
        ];
    }
}
