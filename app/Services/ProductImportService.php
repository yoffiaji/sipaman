<?php

namespace App\Services;

use App\Imports\PirtCommitmentStatusImport;
use App\Imports\ProdukImport;
use App\Models\ImportLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportService
{
    public function importRekapPirt(UploadedFile $file): array
    {
        return $this->runImport($file, 'rekap_pirt', new ProdukImport(), 'produks');
    }

    public function importCommitmentStatus(UploadedFile $file): array
    {
        return $this->runImport($file, 'status_komitmen', new PirtCommitmentStatusImport(), 'pirt_commitment_statuses');
    }

    private function runImport(UploadedFile $file, string $tipeFile, object $import, string $tabel): array
    {
        DB::transaction(function () use ($file, $import) {
            Excel::import($import, $file);
        });

        $berhasil = method_exists($import, 'getBerhasil') ? $import->getBerhasil() : 0;
        $gagal = method_exists($import, 'getGagal') ? $import->getGagal() : 0;
        $failures = method_exists($import, 'getFailureDetails') ? $import->getFailureDetails() : [];

        ImportLog::create([
            'user_id' => auth()->id(),
            'nama_file' => $file->getClientOriginalName(),
            'jumlah_baris' => $berhasil + $gagal,
            'jumlah_berhasil' => $berhasil,
            'jumlah_gagal' => $gagal,
            'keterangan' => $gagal > 0
                ? "Tipe {$tipeFile}: {$gagal} baris gagal / tidak valid."
                : "Tipe {$tipeFile}: semua baris berhasil diimpor.",
            'imported_at' => now(),
        ]);

        return [
            'tipe_file' => $tipeFile,
            'tabel' => $tabel,
            'nama_file' => $file->getClientOriginalName(),
            'berhasil' => $berhasil,
            'gagal' => $gagal,
            'failures' => $failures,
        ];
    }
}
