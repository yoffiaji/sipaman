<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\GambarProduk;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductSettingController extends Controller
{
    // ── Daftar semua produk milik user ────────────────────────

    public function index()
    {
        $produks = Produk::ownedBy(Auth::id())
            ->with(['gambarUtama', 'gambarProduks'])
            ->latest()
            ->get();

        return view('user.products.setting', compact('produks'));
    }

    // ── Edit satu produk (harga, nama_toko, gambar) ───────────

    public function edit(int $id)
    {
        $produk = $this->milikSaya($id);
        return view('user.products.setting-edit', compact('produk'));
    }

    public function update(Request $request, int $id)
    {
        $produk = $this->milikSaya($id);

        $request->validate([
            'nama_toko' => ['required', 'string', 'max:255'],
            'harga'     => ['nullable', 'numeric', 'min:0'],
        ], [
            'nama_toko.required' => 'Nama toko tidak boleh kosong.',
            'harga.numeric'      => 'Harga harus berupa angka.',
            'harga.min'          => 'Harga tidak boleh negatif.',
        ]);

        $produk->nama_toko = $request->nama_toko;
        $produk->harga     = $request->harga;
        $produk->save();

        return back()->with('success', 'Informasi produk berhasil disimpan.');
    }

    // ── Upload gambar baru ────────────────────────────────────

    public function uploadGambar(Request $request, int $id)
    {
        $produk = $this->milikSaya($id);

        $request->validate([
            'gambar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'gambar.required' => 'Pilih gambar terlebih dahulu.',
            'gambar.image'    => 'File harus berupa gambar.',
            'gambar.mimes'    => 'Format harus jpeg, png, jpg, atau webp.',
            'gambar.max'      => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = $request->file('gambar')
            ->store("produk/{$produk->id}", 'public');

        $isFirst = $produk->gambarProduks()->count() === 0;

        GambarProduk::create([
            'produk_id'   => $produk->id,
            'url_gambar'  => $path,
            'is_primary'  => $isFirst,
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Gambar berhasil diupload.');
    }

    // ── Set gambar sebagai gambar utama ───────────────────────

    public function setUtama(int $produkId, int $gambarId)
    {
        $produk = $this->milikSaya($produkId);

        $produk->gambarProduks()->update(['is_primary' => false]);
        $produk->gambarProduks()->where('id', $gambarId)->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama berhasil diubah.');
    }

    // ── Hapus gambar ──────────────────────────────────────────

    public function hapusGambar(int $produkId, int $gambarId)
    {
        $produk = $this->milikSaya($produkId);
        $gambar = $produk->gambarProduks()->findOrFail($gambarId);

        Storage::disk('public')->delete($gambar->url_gambar);
        $wasUtama = $gambar->is_primary;
        $gambar->delete();

        // Kalau yang dihapus adalah gambar utama, promosikan yang tersisa
        if ($wasUtama) {
            $sisa = $produk->gambarProduks()->latest('uploaded_at')->first();
            if ($sisa) {
                $sisa->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    // ── Helper: pastikan produk milik user yang login ─────────

    private function milikSaya(int $id): Produk
    {
        return Produk::ownedBy(Auth::id())
            ->with('gambarProduks')
            ->findOrFail($id);
    }
}