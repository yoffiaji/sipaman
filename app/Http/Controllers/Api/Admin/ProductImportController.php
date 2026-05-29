<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportProductRequest;
use App\Services\ProductImportService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;

class ProductImportController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private ProductImportService $productImportService)
    {
    }

    public function rekapPirt(ImportProductRequest $request): JsonResponse
    {
        try {
            $result = $this->productImportService->importRekapPirt($request->file('file'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Import gagal: ' . $e->getMessage()], 422);
        }

        $this->logAudit('import', 'produks', null, null, $result);

        return response()->json(['message' => 'Import Rekap PIRT selesai.', 'data' => $result]);
    }
}
