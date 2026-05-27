<?php

namespace App\Imports;

use App\Models\JenisBarang;
use App\Models\Produk;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import file "Rekap Data PIRT Diterbitkan".
 *
 * Struktur file:
 * - Baris 1: Judul "REKAP DATA PIRT DITERBITKAN"
 * - Baris 2: Kosong
 * - Baris 3: Header utama
 * - Baris 4: Sub-header DATA PRODUK PANGAN
 * - Baris 5+: Data produk
 *
 * Setelah import:
 * - Produk baru masuk dengan is_verified = false (status: belum_verifikasi)
 * - Produk yang sudah ada (berdasar no_sppirt) diperbarui datanya
 * - is_verified TIDAK diubah jika produk sudah pernah diverifikasi
 */
class ProdukImport implements SkipsEmptyRows, ToCollection, WithStartRow
{
    use Importable;

    private int $berhasil = 0;
    private int $gagal = 0;
    private array $failureDetails = [];

    /** Data asli mulai dari baris 5. */
    public function startRow(): int
    {
        return 5;
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
                // Mapping kolom file Rekap Data PIRT Diterbitkan:
                // A(0) No, B(1) No SPPIRT, C(2) Nama Branding Produk,
                // D(3) Kategori Pangan, E(4) Jenis Pangan, F(5) Kemasan,
                // G(6) Cara Penyimpanan, H(7) NIB, I(8) Wilayah,
                // J(9) Tanggal Pengajuan, K(10) Status OSS,
                // L(11) No HP, M(12) Nama Pelaku Usaha, N(13) Alamat.
                $noSppirt       = $this->cleanString($this->valueAt($row, 1));
                $namaBranding   = $this->cleanString($this->valueAt($row, 2));
                $kategoriPangan = $this->cleanString($this->valueAt($row, 3));
                $jenisPangan    = $this->cleanString($this->valueAt($row, 4));
                $kemasan        = $this->cleanString($this->valueAt($row, 5));
                $caraPenyimpanan= $this->cleanString($this->valueAt($row, 6));
                $nib            = $this->cleanString($this->valueAt($row, 7));
                $wilayah        = $this->cleanString($this->valueAt($row, 8));
                $tanggalPengajuan = $this->parseTanggal($this->valueAt($row, 9));
                $statusOss      = $this->cleanString($this->valueAt($row, 10));
                $noHp           = $this->cleanString($this->valueAt($row, 11));
                $namaPelakuUsaha= $this->cleanString($this->valueAt($row, 12));
                $alamat         = $this->cleanString($this->valueAt($row, 13));

                // Skip baris benar-benar kosong (merge cell / lanjutan header)
                if (! $noSppirt && ! $namaBranding && ! $namaPelakuUsaha && ! $alamat) {
                    continue;
                }

                // Baris tidak valid — data utama tidak lengkap
                if (! $noSppirt || ! $namaBranding || ! $namaPelakuUsaha || ! $alamat) {
                    $this->addFailure(
                        $barisExcel,
                        'required',
                        'Baris dilewati: No SPPIRT, nama branding, nama pelaku usaha, dan alamat wajib diisi.',
                        $row
                    );
                    continue;
                }

                $jenisBarang = $jenisPangan
                    ? JenisBarang::firstOrCreate(['nama_jenis' => $jenisPangan])
                    : null;

                // Cek apakah produk sudah ada
                $produkExisting = Produk::where('no_sppirt', $noSppirt)->first();

                $dataUpdate = [
                    'nama_branding'     => $namaBranding,
                    'kategori_pangan'   => $kategoriPangan,
                    'jenis_pangan'      => $jenisPangan,
                    'kemasan'           => $kemasan,
                    'cara_penyimpanan'  => $caraPenyimpanan,
                    'wilayah'           => $wilayah,
                    'kecamatan_id'      => null, // File rekap tidak punya kolom kecamatan
                    'jenis_barang_id'   => $jenisBarang?->id,
                    'nama_pelaku_usaha' => $namaPelakuUsaha,
                    'alamat'            => $alamat,
                    'nib'               => $nib,
                    'no_hp'             => $noHp,
                    'nama_toko'         => $namaBranding,
                    'alamat_toko'       => $alamat,
                    'tanggal_pengajuan' => $tanggalPengajuan,
                    'status_oss'        => $statusOss,
                ];

                if ($produkExisting) {
                    // Produk sudah ada: perbarui data tapi JANGAN ubah is_verified
                    // (bisa jadi sudah diverifikasi sebelumnya — jangan reset)
                    $produkExisting->update($dataUpdate);
                } else {
                    // Produk baru: masuk dengan status belum_verifikasi
                    $dataUpdate['is_verified'] = false;
                    Produk::create(array_merge(['no_sppirt' => $noSppirt], $dataUpdate));
                }

                $this->berhasil++;
            } catch (\Throwable $e) {
                $this->addFailure($barisExcel, 'exception', $e->getMessage(), $row);
            }
        }
    }

    public function getBerhasil(): int { return $this->berhasil; }
    public function getGagal(): int { return $this->gagal; }
    public function getFailureDetails(): array { return $this->failureDetails; }

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
        if (is_float($value) || is_int($value)) {
            return number_format((float) $value, 0, '', '');
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function parseTanggal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if ($value instanceof \DateTime) {
                return $value->format('Y-m-d');
            }
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }
            $value = trim((string) $value);
            foreach (['d-m-Y', 'd/m/Y', 'Y-m-d', 'm/d/Y'] as $format) {
                try {
                    return Carbon::createFromFormat($format, $value)->toDateString();
                } catch (\Throwable) {}
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
