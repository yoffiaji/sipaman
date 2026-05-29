<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ImportLog::with('user')
            ->when($request->filled('tipe_file'), function ($query) use ($request) {
                $tipeFile = $request->query('tipe_file');

                $query->where(function ($query) use ($tipeFile) {
                    $query->where('tipe_file', $tipeFile)
                        ->orWhere('keterangan', 'like', "%{$tipeFile}%");
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');

                $query->where(function ($query) use ($search) {
                    $query->where('nama_file', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->latest('imported_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.import-logs.index', compact('logs'));
    }
}
