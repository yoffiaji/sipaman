<?php

namespace App\Services;

use App\Imports\PirtCommitmentStatusImport;
use App\Imports\ProdukImport;
use App\Models\ImportLog;
use App\Support\Imports\SpreadsheetFileResolver;
use App\Support\Imports\SpreadsheetTemplateValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class ProductImportService
{
    public function __construct(private SpreadsheetTemplateValidator $templateValidator)
    {
    }

    public function importRekapPirt(UploadedFile $file): array
    {
        return $this->runImport(
            file: $file,
            tipeFile: 'rekap_pirt',
            import: new ProdukImport(),
            tabel: 'produks',
            schemaValidator: fn (string $readerType) => $this->templateValidator->assertRekapPirt($file, $readerType)
        );
    }

    public function importCommitmentStatus(UploadedFile $file): array
    {
        return $this->runImport(
            file: $file,
            tipeFile: 'status_komitmen',
            import: new PirtCommitmentStatusImport(auth()->id()),
            tabel: 'pirt_commitment_statuses',
            schemaValidator: fn (string $readerType) => $this->templateValidator->assertCommitmentStatus($file, $readerType)
        );
    }

    private function runImport(UploadedFile $file, string $tipeFile, object $import, string $tabel, callable $schemaValidator): array
    {
        $readerType = SpreadsheetFileResolver::resolveReaderType($file);

        try {
            $schemaValidator($readerType);

            DB::transaction(function () use ($file, $import, $readerType) {
                ExcelFacade::import($import, $file, null, $readerType);
            });
        } catch (\Throwable $e) {
            $this->createImportLog(
                file: $file,
                tipeFile: $tipeFile,
                berhasil: 0,
                gagal: 0,
                keterangan: "Tipe {$tipeFile}: import gagal. {$e->getMessage()}"
            );

            throw $e;
        }

        $berhasil = method_exists($import, 'getBerhasil') ? $import->getBerhasil() : 0;
        $gagal = method_exists($import, 'getGagal') ? $import->getGagal() : 0;
        $failures = method_exists($import, 'getFailureDetails') ? $import->getFailureDetails() : [];
        $userBaruDibuat = method_exists($import, 'getUserBaruDibuat') ? $import->getUserBaruDibuat() : 0;

        $this->createImportLog(
            file: $file,
            tipeFile: $tipeFile,
            berhasil: $berhasil,
            gagal: $gagal,
            keterangan: $gagal > 0
                ? "Tipe {$tipeFile}: {$gagal} baris gagal / tidak valid."
                : "Tipe {$tipeFile}: semua baris berhasil diimpor."
        );

        return [
            'tipe_file' => $tipeFile,
            'tabel' => $tabel,
            'nama_file' => $file->getClientOriginalName(),
            'reader_type' => $readerType,
            'berhasil' => $berhasil,
            'gagal' => $gagal,
            'user_baru_dibuat' => $userBaruDibuat,
            'failures' => $failures,
        ];
    }

    private function createImportLog(
        UploadedFile $file,
        string $tipeFile,
        int $berhasil,
        int $gagal,
        string $keterangan
    ): void {
        ImportLog::create([
            'user_id' => auth()->id(),
            'tipe_file' => $tipeFile,
            'nama_file' => $file->getClientOriginalName(),
            'jumlah_baris' => $berhasil + $gagal,
            'jumlah_berhasil' => $berhasil,
            'jumlah_gagal' => $gagal,
            'keterangan' => $keterangan,
            'imported_at' => now(),
        ]);
    }
}
