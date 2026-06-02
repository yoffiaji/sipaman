<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJenisBarangRequest;
use App\Http\Requests\Admin\UpdateJenisBarangRequest;
use App\Models\JenisBarang;
use App\Models\Produk;
use App\Services\JenisBarangManagementService;
use App\Support\ProductTypeClassifier;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisBarangController extends Controller
{
    use LogsAuditTrail;

    public function __construct(
        private JenisBarangManagementService $jenisBarangService,
        private ProductTypeClassifier $classifier
    ) {}

    public function index(): View
    {
        $fallback = $this->classifier->fallbackCategory();
        $jenisBarangs = JenisBarang::withCount(['produks', 'aliases'])
            ->orderBy('nama_jenis')
            ->paginate(15);

        return view('admin.jenis-barang.index', [
            'jenisBarangs' => $jenisBarangs,
            'fallback' => $fallback,
            'fallbackProductsCount' => $fallback->produks()->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.jenis-barang.create');
    }

    public function store(StoreJenisBarangRequest $request): RedirectResponse
    {
        $jenisBarang = $this->jenisBarangService->create($request->validated());
        $this->logAudit('create', 'jenis_barangs', $jenisBarang->id, null, $jenisBarang->toArray());

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil ditambahkan.');
    }

    public function edit(JenisBarang $jenisBarang): View
    {
        $jenisBarang->load(['aliases' => fn ($query) => $query->orderBy('priority')]);

        return view('admin.jenis-barang.edit', compact('jenisBarang'));
    }

    public function update(UpdateJenisBarangRequest $request, JenisBarang $jenisBarang): RedirectResponse
    {
        $before = $jenisBarang->load('aliases')->toArray();
        $updated = $this->jenisBarangService->update($jenisBarang, $request->validated());
        $this->logAudit('update', 'jenis_barangs', $jenisBarang->id, $before, $updated->toArray());

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil diperbarui.');
    }

    public function review(Request $request): View
    {
        $fallback = $this->classifier->fallbackCategory();

        $products = Produk::with(['kecamatan', 'jenisBarang'])
            ->where('jenis_barang_id', $fallback->id)
            ->search($request->query('search'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.jenis-barang.review', compact('fallback', 'products'));
    }

    public function sync(): RedirectResponse
    {
        $result = $this->classifier->reclassifyExistingProducts();

        $this->logAudit('update', 'produks', null, null, [
            'aksi' => 'sinkron_ulang_jenis_produk',
            ...$result,
        ]);

        return redirect()
            ->route('admin.jenis-barang.index')
            ->with('success', "Sinkron ulang selesai. {$result['checked']} produk diperiksa, {$result['updated']} produk diperbarui, {$result['fallback']} produk masih perlu review.");
    }

    public function destroy(JenisBarang $jenisBarang): RedirectResponse
    {
        if ($jenisBarang->produks()->exists()) {
            return back()->withErrors(['jenis_barang' => 'Jenis barang tidak bisa dihapus karena masih dipakai produk.']);
        }

        $before = $jenisBarang->toArray();
        $jenisBarang->delete();
        $this->jenisBarangService->forgetCatalogCache();
        $this->logAudit('delete', 'jenis_barangs', $before['id'], $before, null);

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil dihapus.');
    }
}
