<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportCommitmentStatusRequest;
use App\Services\ProductImportService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;

class ProductVerificationController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImportService $productImportService)
    {
    }

    public function import(ImportCommitmentStatusRequest $request): JsonResponse
    {
        try {
            $result = $this->productImportService->importCommitmentStatus($request->file('file'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Import gagal: ' . $e->getMessage()], 422);
        }

        $this->logAudit('import', 'pirt_commitment_statuses', null, null, $result);

        return response()->json(['message' => 'Import Status Komitmen selesai.', 'data' => $result]);
    }
}
