<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    public function index(Request $request): View
    {
        $audits = AuditTrail::with('user')
            ->when($request->filled('aksi'), fn ($query) => $query->where('aksi', $request->query('aksi')))
            ->when($request->filled('tabel'), fn ($query) => $query->where('tabel_terkait', $request->query('tabel')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $activities = ActivityLog::with('user')->latest()->limit(10)->get();

        return view('super-admin.audit-trails.index', compact('audits', 'activities'));
    }
}
