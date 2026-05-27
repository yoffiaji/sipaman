<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJenisBarangRequest;
use App\Http\Requests\Admin\UpdateJenisBarangRequest;
use App\Models\JenisBarang;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JenisBarangController extends Controller
{
    use LogsAuditTrail;

    public function index(): View
    {
        $jenisBarangs = JenisBarang::withCount('produks')->orderBy('nama_jenis')->paginate(15);

        return view('admin.jenis-barang.index', compact('jenisBarangs'));
    }

    public function create(): View
    {
        return view('admin.jenis-barang.create');
    }

    public function store(StoreJenisBarangRequest $request): RedirectResponse
    {
        $jenisBarang = JenisBarang::create($request->validated());
        $this->logAudit('create', 'jenis_barangs', $jenisBarang->id, null, $jenisBarang->toArray());

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil ditambahkan.');
    }

    public function edit(JenisBarang $jenisBarang): View
    {
        return view('admin.jenis-barang.edit', compact('jenisBarang'));
    }

    public function update(UpdateJenisBarangRequest $request, JenisBarang $jenisBarang): RedirectResponse
    {
        $before = $jenisBarang->toArray();
        $jenisBarang->update($request->validated());
        $this->logAudit('update', 'jenis_barangs', $jenisBarang->id, $before, $jenisBarang->fresh()->toArray());

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil diperbarui.');
    }

    public function destroy(JenisBarang $jenisBarang): RedirectResponse
    {
        if ($jenisBarang->produks()->exists()) {
            return back()->withErrors(['jenis_barang' => 'Jenis barang tidak bisa dihapus karena masih dipakai produk.']);
        }

        $before = $jenisBarang->toArray();
        $jenisBarang->delete();
        $this->logAudit('delete', 'jenis_barangs', $before['id'], $before, null);

        return redirect()->route('admin.jenis-barang.index')->with('success', 'Jenis barang berhasil dihapus.');
    }
}
