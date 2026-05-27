<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function auditTrails(Request $request): JsonResponse
    {
        $query = AuditTrail::with('user:id,nama,email')->latest();

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->query('aksi'));
        }
        if ($request->filled('tabel')) {
            $query->where('tabel_terkait', $request->query('tabel'));
        }

        return response()->json($query->paginate($request->query('per_page', 30)));
    }

    public function activityLogs(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user:id,nama,email')->latest();

        if ($request->filled('search')) {
            $query->where('aktivitas', 'like', '%' . $request->query('search') . '%');
        }

        return response()->json($query->paginate($request->query('per_page', 30)));
    }
}
