@extends('layouts.public')
@section('title', 'Atur Produk — ' . $produk->nama_branding)
@section('content')
<section class="mx-auto max-w-container px-4 pt-10 md:px-6">
    <div class="relative overflow-hidden rounded-3xl border border-outline-variant bg-gradient-to-br from-primary-soft via-white to-secondary-soft p-8 md:p-10">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-accent/25 blur-3xl"></div>
        <div class="relative">
            <x-badge-status status="info">Konfigurasi Produk</x-badge-status>
            <h1 class="font-display mt-4 text-3xl font-700 text-ink md:text-4xl">{{ $produk->nama_branding }}</h1>
            <p class="mt-3 text-on-surface-variant">No. SPPIRT: {{ $produk->no_sppirt }}</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-container px-4 py-8 md:px-6">
    <div class="mb-6">
        <a href="{{ route('user.products.setting.index') }}" class="inline-flex items-center gap-1.5 text-sm font-600 text-on-surface-variant transition-colors hover:text-primary">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Daftar
        </a>
    </div>

    @if (session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_1.4fr]">

        {{-- ── Kartu: Harga & Nama Toko ─────────────────────── --}}
        <div class="overflow-hidden rounded-3xl border border-outline-variant bg-white shadow-soft">
            <div class="flex items-center gap-2 border-b border-outline-variant px-6 py-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <span class="material-symbols-outlined text-[20px]">sell</span>
                </span>
                <h2 class="font-display text-xl font-700 text-ink">Harga & Toko</h2>
            </div>

            <form method="POST" action="{{ route('user.products.setting.update', $produk->id) }}" class="space-y-4 p-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="nama_toko" class="mb-1.5 block text-sm font-600 text-on-surface">Nama Toko</label>
                    <input
                        type="text" id="nama_toko" name="nama_toko"
                        value="{{ old('nama_toko', $produk->nama_toko) }}"
                        class="w-full rounded-xl border border-outline-variant bg-white px-3.5 py-3 text-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 @error('nama_toko') border-red-400 @enderror"
                        placeholder="Nama toko Anda"
                    >
                    @error('nama_toko')
                        <p class="mt-1.5 flex items-center gap-1 text-xs font-600 text-red-600">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="harga" class="mb-1.5 block text-sm font-600 text-on-surface">Harga (Rp)</label>
                    <input
                        type="number" id="harga" name="harga" min="0" step="1"
                        value="{{ old('harga', $produk->harga) }}"
                        class="w-full rounded-xl border border-outline-variant bg-white px-3.5 py-3 text-sm text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 @error('harga') border-red-400 @enderror"
                        placeholder="Kosongkan jika belum ada harga"
                    >
                    @error('harga')
                        <p class="mt-1.5 flex items-center gap-1 text-xs font-600 text-red-600">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    @enderror
                    <p class="mt-1.5 text-xs text-on-surface-variant">Jika dikosongkan, harga akan ditampilkan sebagai <span class="font-700">NA</span>.</p>
                </div>

                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-600 text-white transition-colors hover:bg-primary-container">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Informasi
                </button>
            </form>
        </div>

        {{-- ── Kartu: Kelola Gambar ─────────────────────────── --}}
        <div class="overflow-hidden rounded-3xl border border-outline-variant bg-white shadow-soft">
            <div class="flex items-center gap-2 border-b border-outline-variant px-6 py-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-secondary-soft text-secondary">
                    <span class="material-symbols-outlined text-[20px]">photo_library</span>
                </span>
                <h2 class="font-display text-xl font-700 text-ink">Gambar Produk</h2>
            </div>

            <div class="space-y-6 p-6">
                {{-- Form upload gambar baru --}}
                <form method="POST" action="{{ route('user.products.setting.upload-gambar', $produk->id) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <label for="gambar" class="block text-sm font-600 text-on-surface">Tambah Gambar Baru</label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input
                            type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="block w-full text-sm text-on-surface-variant file:mr-3 file:rounded-lg file:border-0 file:bg-primary-soft file:px-4 file:py-2 file:text-sm file:font-600 file:text-primary hover:file:bg-primary hover:file:text-white @error('gambar') text-red-600 @enderror"
                        >
                        <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-sm font-600 text-white transition-colors hover:bg-primary-container">
                            <span class="material-symbols-outlined text-[18px]">upload</span>
                            Upload
                        </button>
                    </div>
                    @error('gambar')
                        <p class="flex items-center gap-1 text-xs font-600 text-red-600">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    @enderror
                    <p class="text-xs text-on-surface-variant">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                </form>

                {{-- Galeri gambar yang sudah ada --}}
                <div>
                    <p class="mb-3 text-sm font-600 text-on-surface">Galeri ({{ $produk->gambarProduks->count() }})</p>

                    @if ($produk->gambarProduks->isEmpty())
                        <div class="rounded-2xl border border-dashed border-outline-variant bg-surface-container-low p-8 text-center">
                            <span class="material-symbols-outlined text-[36px] text-outline">add_photo_alternate</span>
                            <p class="mt-2 text-sm font-600 text-on-surface">Belum ada gambar</p>
                            <p class="mt-1 text-xs text-on-surface-variant">Upload gambar pertama untuk produk ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($produk->gambarProduks as $gambar)
                                <div class="group relative overflow-hidden rounded-2xl border {{ $gambar->is_primary ? 'border-primary ring-2 ring-primary/30' : 'border-outline-variant' }}">
                                    <div class="aspect-square overflow-hidden bg-surface-container">
                                        <img src="{{ $gambar->gambar_url }}" alt="Gambar produk" class="h-full w-full object-cover">
                                    </div>

                                    @if ($gambar->is_primary)
                                        <span class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-full bg-primary px-2 py-0.5 text-[10px] font-700 text-white">
                                            <span class="material-symbols-outlined text-[12px]">star</span>
                                            Utama
                                        </span>
                                    @endif

                                    {{-- Aksi muncul saat hover --}}
                                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-end gap-1 bg-gradient-to-t from-ink/70 to-transparent p-2 opacity-0 transition-opacity group-hover:opacity-100">
                                        @unless ($gambar->is_primary)
                                            <form method="POST" action="{{ route('user.products.setting.set-utama', [$produk->id, $gambar->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Jadikan gambar utama"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/90 text-on-surface-variant transition-colors hover:bg-primary hover:text-white">
                                                    <span class="material-symbols-outlined text-[17px]">star</span>
                                                </button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('user.products.setting.hapus-gambar', [$produk->id, $gambar->id]) }}"
                                              onsubmit="return confirm('Hapus gambar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus gambar"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/90 text-on-surface-variant transition-colors hover:bg-red-600 hover:text-white">
                                                <span class="material-symbols-outlined text-[17px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
