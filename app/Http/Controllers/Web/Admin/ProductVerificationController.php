<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportCommitmentStatusRequest;
use App\Http\Requests\Admin\UpdateProductVerificationRequest;
use App\Models\ImportLog;
use App\Models\Produk;
use App\Services\ProductImportService;
use App\Services\ProductVerificationService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductVerificationController extends Controller
{
    use LogsAuditTrail;

    public function __construct(
        private ProductImportService $productImportService,
        private ProductVerificationService $productVerificationService
    ) {
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'semua');

        $query = Produk::with(['kecamatan', 'verifikasi.verifikator', 'commitmentStatus']);

        match ($tab) {
            'terverifikasi' => $query->where('is_verified', true),
            'belum' => $query->where('is_verified', false)->whereDoesntHave('verifikasi'),
            'proses' => $query->where('is_verified', false)->whereHas('verifikasi'),
            default => null,
        };

        $products = $query
            ->search($request->query('search'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Produk::count(),
            'terverifikasi' => Produk::where('is_verified', true)->count(),
            'belum' => Produk::where('is_verified', false)->whereDoesntHave('verifikasi')->count(),
            'proses' => Produk::where('is_verified', false)->whereHas('verifikasi')->count(),
        ];

        $lastImport = ImportLog::with('user')
            ->where(function ($query) {
                $query->where('tipe_file', 'status_komitmen')
                    ->orWhere('keterangan', 'like', '%status_komitmen%');
            })
            ->latest('imported_at')
            ->first();

        return view('admin.verifications.index', compact('products', 'stats', 'lastImport', 'tab'));
    }

    public function import(ImportCommitmentStatusRequest $request): RedirectResponse
    {
        try {
            $result = $this->productImportService->importCommitmentStatus($request->file('file'));
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['file' => 'Import gagal: ' . $e->getMessage()]);
        }

        $this->logAudit('import', 'pirt_commitment_statuses', null, null, $result);

        return back()
            ->with('success', "Import Status Pemenuhan Komitmen selesai. Berhasil: {$result['berhasil']}, gagal: {$result['gagal']}, user baru dibuat: {$result['user_baru_dibuat']}.")
            ->with('import_failures', array_slice($result['failures'], 0, 5));
    }

    public function edit(Produk $produk): View
    {
        $produk->load(['verifikasi.verifikator', 'commitmentStatus', 'kecamatan']);

        return view('admin.verifications.edit', compact('produk'));
    }

    public function update(UpdateProductVerificationRequest $request, Produk $produk): RedirectResponse
    {
        $before = $produk->load('verifikasi')->toArray();
        $verifikasi = $this->productVerificationService->update($produk, $request->validated());

        $this->logAudit('verify_manual', 'produks', $produk->id, $before, $verifikasi->toArray());

        $message = $verifikasi->status_komitmen
            ? 'Produk berhasil diverifikasi dan sekarang tampil di katalog publik.'
            : 'Data verifikasi disimpan. Produk belum sepenuhnya lulus.';

        return redirect()->route('admin.verifications.index')->with('success', $message);
    }
}
