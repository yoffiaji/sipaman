<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('search'), fn ($query) => $query->where('aktivitas', 'like', '%' . $request->query('search') . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
