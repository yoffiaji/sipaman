<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportCommitmentStatusRequest;
use App\Http\Requests\Admin\UpdateProductVerificationRequest;
use App\Models\Produk;
use App\Services\ProductImportService;
use App\Services\ProductVerificationService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVerificationController extends Controller
{
    use LogsAuditTrail;

    public function __construct(
        private ProductImportService $productImportService,
        private ProductVerificationService $productVerificationService
    ) {
    }

    public function import(ImportCommitmentStatusRequest $request): JsonResponse
    {
        $result = $this->productImportService->importCommitmentStatus($request->file('file'));
        $this->logAudit('import', 'pirt_commitment_statuses', null, null, $result);

        return response()->json(['message' => 'Import Status Komitmen selesai.', 'data' => $result]);
    }

    public function update(UpdateProductVerificationRequest $request, Produk $produk): JsonResponse
    {
        $before = $produk->load('verifikasi')->toArray();
        $verifikasi = $this->productVerificationService->update($produk, $request->validated());
        $this->logAudit('verify', 'produks', $produk->id, $before, $verifikasi->toArray());

        return response()->json([
            'message' => $verifikasi->status_komitmen ? 'Produk berhasil diverifikasi.' : 'Data verifikasi disimpan, produk belum lulus semua syarat.',
            'data' => $produk->fresh(['verifikasi.verifikator', 'commitmentStatus']),
        ]);
    }

    public function reject(Request $request, Produk $produk): JsonResponse
    {
        $data = $request->validate(['catatan' => ['nullable', 'string', 'max:1000']]);
        $verifikasi = $this->productVerificationService->reject($produk, $data['catatan'] ?? null);
        $this->logAudit('reject', 'produks', $produk->id, null, $verifikasi->toArray());

        return response()->json(['message' => 'Produk ditolak dan status verifikasi direset.']);
    }
}
