@extends('layouts.public')

@section('title', 'Karanganyar Portal | Katalog PIRT')

@section('content')
    @include('partials.public.hero')

    {{-- Stats strip --}}
    <section class="mx-auto max-w-container px-4 md:px-6">
        <div class="-mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ([
                ['value' => number_format($featuredProducts->count()) . '+', 'label' => 'Produk Unggulan', 'icon' => 'inventory_2', 'tone' => 'bg-primary-soft text-primary'],
                ['value' => '17', 'label' => 'Kecamatan', 'icon' => 'map', 'tone' => 'bg-secondary-soft text-secondary'],
                ['value' => 'PIRT', 'label' => 'Terverifikasi', 'icon' => 'verified', 'tone' => 'bg-primary-soft text-primary'],
                ['value' => 'UMKM', 'label' => 'Karanganyar', 'icon' => 'storefront', 'tone' => 'bg-secondary-soft text-secondary'],
            ] as $stat)
                <div class="rounded-2xl border border-outline-variant bg-white p-5 text-center shadow-soft transition hover:-translate-y-1 hover:shadow-lift">
                    <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl {{ $stat['tone'] }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $stat['icon'] }}</span>
                    </span>
                    <p class="font-display mt-3 text-3xl font-700 text-ink">{{ $stat['value'] }}</p>
                    <p class="eyebrow mt-1 text-[10px] font-600 text-on-surface-variant">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Featured products --}}
    <section class="mx-auto max-w-container px-4 py-16 md:px-6">
        <div class="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <div class="max-w-xl">
                <p class="eyebrow text-[11px] font-600 text-secondary">Direktori</p>
                <h2 class="font-display mt-2 text-3xl font-700 text-ink md:text-4xl">Produk Unggulan</h2>
                <p class="mt-3 leading-7 text-on-surface-variant">Produk lokal Karanganyar yang sudah terverifikasi dan siap dikenalkan ke publik.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 self-start rounded-full bg-primary px-5 py-2.5 font-600 text-white transition-colors hover:bg-primary-container md:self-auto">
                Lihat semua produk
                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
            </a>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse ($featuredProducts as $product)
                <x-product-card
                    :name="$product->nama_branding"
                    :district="$product->kecamatan?->nama_kecamatan ?? $product->wilayah ?? 'Karanganyar'"
                    :price="$product->harga ? 'Rp ' . number_format($product->harga, 0, ',', '.') : null"
                    :description="$product->deskripsi ?? $product->jenis_pangan"
                    :image="$product->gambarUtama?->gambar_url"
                    :href="route('products.show', $product)"
                />
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-outline-variant bg-white p-12 text-center">
                    <span class="material-symbols-outlined text-[40px] text-outline">inventory_2</span>
                    <p class="mt-3 font-600 text-on-surface">Belum ada produk terverifikasi</p>
                    <p class="mt-1 text-sm text-on-surface-variant">Import dan verifikasi data produk terlebih dahulu.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Potensi lokal --}}
    <section class="mx-auto max-w-container px-4 pb-20 md:px-6">
        <div class="relative overflow-hidden rounded-3xl bg-primary p-8 text-white md:p-12">
            <div class="absolute -right-10 -top-10 h-56 w-56 rounded-full bg-accent/30 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-8 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative grid gap-10 md:grid-cols-[1.2fr_1fr] md:items-center">
                <div>
                    <p class="eyebrow text-[11px] font-600 text-accent">Sebaran Wilayah</p>
                    <h2 class="font-display mt-2 text-3xl font-700 md:text-4xl">Potensi Lokal Tiap Kecamatan</h2>
                    <p class="mt-4 max-w-2xl leading-8 text-white/85">Katalog ini membantu masyarakat melihat produk PIRT, pelaku usaha, dan persebaran potensi dari wilayah Karanganyar.</p>
                    <a href="{{ route('umkm.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-accent px-5 py-2.5 font-600 text-ink transition-colors hover:bg-white">
                        Jelajahi UMKM
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['Kuliner', 'restaurant'],
                        ['Kerajinan', 'palette'],
                        ['Minuman', 'local_cafe'],
                        ['Olahan Pangan', 'bakery_dining'],
                    ] as $item)
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-5 transition hover:-translate-y-1 hover:bg-white/20">
                            <span class="material-symbols-outlined text-[26px] text-accent">{{ $item[1] }}</span>
                            <p class="mt-2 font-display text-lg font-600">{{ $item[0] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
