@csrf
@if (($method ?? 'POST') !== 'POST')
    @method($method)
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700">No SPPIRT</label>
        <input name="no_sppirt" value="{{ old('no_sppirt', $produk->no_sppirt ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nama Branding</label>
        <input name="nama_branding" value="{{ old('nama_branding', $produk->nama_branding ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Kategori Pangan</label>
        <input name="kategori_pangan" value="{{ old('kategori_pangan', $produk->kategori_pangan ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Jenis Pangan</label>
        <input name="jenis_pangan" value="{{ old('jenis_pangan', $produk->jenis_pangan ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Jenis Barang</label>
        <select name="jenis_barang_id" class="mt-1 w-full rounded-lg border-slate-300">
            <option value="">- Pilih -</option>
            @foreach ($jenisBarangs as $jenisBarang)
                <option value="{{ $jenisBarang->id }}" @selected((string) old('jenis_barang_id', $produk->jenis_barang_id ?? '') === (string) $jenisBarang->id)>
                    {{ $jenisBarang->nama_jenis }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Kecamatan</label>
        <select name="kecamatan_id" class="mt-1 w-full rounded-lg border-slate-300">
            <option value="">- Belum dipetakan -</option>
            @foreach ($kecamatans as $kecamatan)
                <option value="{{ $kecamatan->id }}" @selected((string) old('kecamatan_id', $produk->kecamatan_id ?? '') === (string) $kecamatan->id)>
                    {{ $kecamatan->nama_kecamatan }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Kemasan</label>
        <input name="kemasan" value="{{ old('kemasan', $produk->kemasan ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Cara Penyimpanan</label>
        <input name="cara_penyimpanan" value="{{ old('cara_penyimpanan', $produk->cara_penyimpanan ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Wilayah</label>
        <input name="wilayah" value="{{ old('wilayah', $produk->wilayah ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">NIB</label>
        <input name="nib" value="{{ old('nib', $produk->nib ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">No HP</label>
        <input name="no_hp" value="{{ old('no_hp', $produk->no_hp ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Harga</label>
        <input type="number" min="0" name="harga" value="{{ old('harga', $produk->harga ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Tanggal Pengajuan</label>
        <input type="date" name="tanggal_pengajuan" value="{{ old('tanggal_pengajuan', isset($produk?->tanggal_pengajuan) ? $produk->tanggal_pengajuan?->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Masa Berlaku PIRT</label>
        <input type="date" name="masa_berlaku_pirt" value="{{ old('masa_berlaku_pirt', isset($produk?->masa_berlaku_pirt) ? $produk->masa_berlaku_pirt?->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nama Pelaku Usaha</label>
        <input name="nama_pelaku_usaha" value="{{ old('nama_pelaku_usaha', $produk->nama_pelaku_usaha ?? '') }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Nama Toko</label>
        <input name="nama_toko" value="{{ old('nama_toko', $produk->nama_toko ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700">Alamat Legal</label>
        <textarea name="alamat" rows="4" required class="mt-1 w-full rounded-lg border-slate-300">{{ old('alamat', $produk->alamat ?? '') }}</textarea>
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700">Alamat Toko</label>
        <textarea name="alamat_toko" rows="4" class="mt-1 w-full rounded-lg border-slate-300">{{ old('alamat_toko', $produk->alamat_toko ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4">
    <label class="text-sm font-semibold text-slate-700">Deskripsi Katalog</label>
    <textarea name="deskripsi" rows="4" class="mt-1 w-full rounded-lg border-slate-300">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
</div>

<div class="mt-4 flex items-center gap-3">
    <input type="hidden" name="is_verified" value="0">
    <input id="is_verified" type="checkbox" name="is_verified" value="1" @checked(old('is_verified', $produk->is_verified ?? false)) class="rounded border-slate-300 text-slate-900">
    <label for="is_verified" class="text-sm font-semibold text-slate-700">Tandai produk terverifikasi</label>
</div>

<div class="mt-6 flex gap-3">
    <button class="rounded-lg bg-slate-900 px-5 py-2.5 font-semibold text-white hover:bg-slate-800" type="submit">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
</div>
