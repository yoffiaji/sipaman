@extends('layouts.admin')

@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')

@section('content')
    <div class="space-y-6">
        @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
        @if ($errors->any()) <x-alert type="danger"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert> @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <h2 class="font-display text-2xl font-bold">{{ $produk->nama_branding }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $produk->no_sppirt }}</p>
                    <div class="mt-3">@if ($produk->is_verified)<x-badge-status status="terverifikasi">Terverifikasi</x-badge-status>@else<x-badge-status status="belum_terverifikasi">Belum Terverifikasi</x-badge-status>@endif</div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.products.edit', $produk) }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Edit</a>
                    <form action="{{ route('admin.products.destroy', $produk) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg border border-red-200 px-4 py-2 font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            </div>

            <dl class="mt-6 grid gap-4 md:grid-cols-3">
                @foreach ([
                    'Kategori' => $produk->kategori_pangan ?? '-',
                    'Jenis Pangan' => $produk->jenis_pangan ?? '-',
                    'Jenis Barang' => $produk->jenisBarang?->nama_jenis ?? '-',
                    'Wilayah' => $produk->kecamatan?->nama_kecamatan ?? $produk->wilayah ?? '-',
                    'Pelaku Usaha' => $produk->nama_pelaku_usaha,
                    'NIB' => $produk->nib ?? '-',
                    'No HP' => $produk->no_hp ?? '-',
                    'Harga' => $produk->harga ? 'Rp ' . number_format($produk->harga, 0, ',', '.') : '-',
                    'Masa Berlaku' => $produk->masa_berlaku_pirt?->format('d/m/Y') ?? '-',
                ] as $label => $value)
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div><h3 class="font-bold">Alamat</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $produk->alamat }}</p></div>
                <div><h3 class="font-bold">Deskripsi</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $produk->deskripsi ?? '-' }}</p></div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-bold">Gambar Produk</h3>
            @if ($produk->is_verified)
                <form action="{{ route('admin.products.images.store', $produk) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 md:flex-row md:items-center">
                    @csrf
                    <input type="file" name="images[]" multiple accept="image/*" required class="block w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:font-semibold">
                    <input type="number" name="primary_index" value="0" min="0" class="w-28 rounded-lg border-slate-300" title="Index gambar utama">
                    <button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Upload</button>
                </form>
            @else
                <x-alert type="warning" class="mt-4">Gambar hanya dapat diunggah setelah produk terverifikasi.</x-alert>
            @endif

            <div class="mt-5 grid gap-4 md:grid-cols-4">
                @forelse ($produk->gambarProduks as $gambar)
                    <div class="overflow-hidden rounded-lg border border-slate-200">
                        <img src="{{ $gambar->gambar_url }}" alt="{{ $produk->nama_branding }}" class="h-40 w-full object-cover">
                        <div class="flex items-center justify-between p-3 text-sm">
                            <span>{{ $gambar->is_primary ? 'Utama' : 'Gambar' }}</span>
                            <form action="{{ route('admin.products.images.destroy', $gambar) }}" method="POST" onsubmit="return confirm('Hapus gambar?')">
                                @csrf @method('DELETE')
                                <button class="font-semibold text-red-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada gambar produk.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
