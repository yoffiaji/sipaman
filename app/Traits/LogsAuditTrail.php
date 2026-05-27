<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

/**
 * Trait LogsAuditTrail
 * --------------------
 * Digunakan oleh semua Controller untuk mencatat:
 *  - logAudit()    → perubahan data ke tabel audit_trails
 *  - logActivity() → aktivitas login/logout ke tabel activity_logs
 */
trait LogsAuditTrail
{
    /**
     * Catat perubahan data (create/update/delete/verify/import).
     */
    protected function logAudit(
        string $aksi,
        string $tabelTerkait,
        ?int $recordId   = null,
        ?array $dataLama  = null,
        ?array $dataBaru  = null
    ): void {
        try {
            AuditTrail::create([
                'user_id'       => auth()->id(),
                'aksi'          => $aksi,
                'tabel_terkait' => $tabelTerkait,
                'record_id'     => $recordId,
                'data_lama'     => $dataLama,
                'data_baru'     => $dataBaru,
                'ip_address'    => app(Request::class)->ip(),
            ]);
        } catch (\Throwable $e) {
            // Jangan sampai gagal log membatalkan transaksi utama
            logger()->error('Audit trail error: ' . $e->getMessage());
        }
    }

    /**
     * Catat aktivitas pengguna (login, logout, dsb).
     */
    protected function logActivity(string $aktivitas, ?int $userId = null): void
    {
        try {
            $request = app(Request::class);
            ActivityLog::create([
                'user_id'    => $userId ?? auth()->id(),
                'aktivitas'  => $aktivitas,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('Activity log error: ' . $e->getMessage());
        }
    }
}
