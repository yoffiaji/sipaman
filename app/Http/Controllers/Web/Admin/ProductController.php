<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\ImportLog;
use App\Models\JenisBarang;
use App\Models\Kecamatan;
use App\Models\Produk;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    use LogsAuditTrail;

    public function index(Request $request): View
    {
        $products = Produk::with(['kecamatan', 'jenisBarang', 'gambarUtama', 'commitmentStatus'])
            ->search($request->query('search'))
            ->byKecamatan($request->query('kecamatan_id'))
            ->byJenisBarang($request->query('jenis_barang_id'))
            ->when($request->query('status') === 'verified', fn ($query) => $query->where('is_verified', true))
            ->when($request->query('status') === 'unverified', fn ($query) => $query->where('is_verified', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Produk::count(),
            'verified' => Produk::where('is_verified', true)->count(),
            'unverified' => Produk::where('is_verified', false)->count(),
        ];

        $lastImport = ImportLog::with('user')
            ->where(function ($query) {
                $query->where('tipe_file', 'rekap_pirt')
                    ->orWhere('keterangan', 'like', '%rekap_pirt%');
            })
            ->latest('imported_at')
            ->first();

        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        $jenisBarangs = JenisBarang::active()->orderBy('nama_jenis')->get();

        return view('admin.products.index', compact('products', 'stats', 'lastImport', 'kecamatans', 'jenisBarangs'));
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $produk = Produk::create($request->validated());

        $this->logAudit('create', 'produks', $produk->id, null, $produk->toArray());

        return redirect()->route('admin.products.show', $produk)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Produk $produk): View
    {
        $produk->load(['kecamatan', 'jenisBarang', 'gambarProduks', 'gambarUtama', 'verifikasi.verifikator', 'commitmentStatus']);

        return view('admin.products.show', compact('produk'));
    }

    public function edit(Produk $produk): View
    {
        return view('admin.products.edit', array_merge($this->formData(), compact('produk')));
    }

    public function update(UpdateProductRequest $request, Produk $produk): RedirectResponse
    {
        $before = $produk->toArray();
        $produk->update($request->validated());

        $this->logAudit('update', 'produks', $produk->id, $before, $produk->fresh()->toArray());

        return redirect()->route('admin.products.show', $produk)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $before = $produk->toArray();
        foreach ($produk->gambarProduks as $gambar) {
            Storage::disk('public')->delete($gambar->url_gambar);
        }
        $produk->delete();

        $this->logAudit('delete', 'produks', $produk->id, $before, null);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'kecamatans' => Kecamatan::orderBy('nama_kecamatan')->get(),
            'jenisBarangs' => JenisBarang::active()->orderBy('nama_jenis')->get(),
        ];
    }
}
