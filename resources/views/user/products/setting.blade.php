@extends('layouts.public')
@section('title', 'Konfigurasi Produk')
@section('content')
<section class="mx-auto max-w-container px-4 pt-10 md:px-6">
    <div class="relative overflow-hidden rounded-3xl border border-outline-variant bg-gradient-to-br from-primary-soft via-white to-secondary-soft p-8 md:p-10">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-accent/25 blur-3xl"></div>
        <div class="relative">
            <x-badge-status status="info">Konfigurasi Produk</x-badge-status>
            <h1 class="font-display mt-4 text-3xl font-700 text-ink md:text-4xl">Atur harga, deskripsi, dan gambar produk</h1>
            <p class="mt-3 max-w-2xl leading-8 text-on-surface-variant">Data resmi PIRT tidak dapat diubah dari akun pelaku usaha. Harga yang masih <span class="font-700">NA</span> belum diatur.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-container px-4 py-8 md:px-6">
    <div class="mb-6">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-600 text-on-surface-variant transition-colors hover:text-primary">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>

    @if (session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <div class="overflow-hidden rounded-3xl border border-outline-variant bg-white shadow-soft">
        <div class="flex items-center justify-between gap-3 border-b border-outline-variant px-6 py-5">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft text-primary">
                    <span class="material-symbols-outlined text-[20px]">tune</span>
                </span>
                <h2 class="font-display text-xl font-700 text-ink">Daftar Produk Saya</h2>
            </div>
            <span class="rounded-full bg-surface-container px-3 py-1 text-xs font-600 text-on-surface-variant">
                {{ $produks->count() }} produk
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-3.5 font-600">Produk</th>
                        <th class="px-6 py-3.5 font-600">Harga</th>
                        <th class="px-6 py-3.5 font-600">Gambar</th>
                        <th class="px-6 py-3.5 text-right font-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($produks as $produk)
                        <tr class="group transition-colors hover:bg-surface-container-low">
                            {{-- Produk + thumbnail --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-surface-container">
                                        @if ($produk->gambarUtama)
                                            <img src="{{ $produk->gambarUtama->gambar_url }}" alt="{{ $produk->nama_branding }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="flex h-full w-full items-center justify-center text-on-surface-variant">
                                                <span class="material-symbols-outlined text-[20px]">image</span>
                                            </span>
                                        @endif
                                    </span>
                                    <span>
                                        <span class="block font-600 text-on-surface">{{ $produk->nama_branding }}</span>
                                        <span class="block text-xs text-on-surface-variant">{{ $produk->no_sppirt }}</span>
                                    </span>
                                </div>
                            </td>

                            {{-- Harga --}}
                            <td class="px-6 py-4">
                                @if ($produk->harga)
                                    <span class="font-600 text-primary">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-700 text-amber-700">
                                        <span class="material-symbols-outlined text-[12px]">warning</span>
                                        NA
                                    </span>
                                @endif
                            </td>

                            {{-- Jumlah gambar --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[16px]">image</span>
                                    {{ $produk->gambarUtama ? 'Sudah ada' : 'Belum ada' }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('user.products.setting.edit', $produk->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-primary-soft px-3 py-2 text-xs font-600 text-primary transition-colors hover:bg-primary hover:text-white">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Atur
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-[40px] text-outline">inventory_2</span>
                                <p class="mt-2 font-600 text-on-surface">Belum ada produk</p>
                                <p class="mt-1 text-sm">Hubungi admin untuk menambahkan produk PIRT Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
