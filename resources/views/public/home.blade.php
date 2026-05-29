@extends('layouts.public')

@section('title', 'Katalog Pangan Aman')

@section('content')
    @include('partials.public.hero')

    @php
        $featuredSection = $contents->get('featured_products');
        $showFeaturedSection = ! $featuredSection || $featuredSection->is_active;
        $featuredSubtitle = $featuredSection?->subjudul ?: 'Direktori';
        $featuredTitle = $featuredSection?->judul ?: 'Produk Pangan Terverifikasi';
        $featuredContent = $featuredSection?->konten ?: 'Produk lokal Karanganyar yang sudah terverifikasi dan siap dikenalkan ke publik.';
        $featuredButtonText = $featuredSection?->button_text ?: 'Lihat Semua Produk';
        $featuredButtonUrl = $featuredSection?->button_url ?: route('products.index');
        $regionSection = $contents->get('region_potential');
        $showRegionSection = ! $regionSection || $regionSection->is_active;
        $regionSubtitle = $regionSection?->subjudul ?: 'Sebaran Wilayah';
        $regionTitle = $regionSection?->judul ?: 'Potensi Lokal Tiap Kecamatan';
        $regionContent = $regionSection?->konten ?: 'SIPAMAN membantu masyarakat melihat produk PIRT, pelaku usaha, dan persebaran potensi pangan aman dari wilayah Karanganyar.';
        $regionButtonText = $regionSection?->button_text ?: 'Jelajahi UMKM';
        $regionButtonUrl = $regionSection?->button_url ?: route('umkm.index');
    @endphp

    {{-- Stats cards --}}
    @php
        $verifiedProductCount = $homeStats['verified_products'] ?? $featuredProducts->count();
        $districtCount = $homeStats['districts'] ?? 17;
        $umkmCount = $homeStats['umkm'] ?? 0;
    @endphp
    <section class="relative z-20 mx-auto -mt-16 max-w-container px-4 md:-mt-20 md:px-6">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ([
                ['value' => number_format($verifiedProductCount), 'label' => 'Produk Terverifikasi', 'icon' => 'inventory_2', 'tone' => 'bg-primary-soft text-primary'],
                ['value' => number_format($districtCount), 'label' => 'Kecamatan', 'icon' => 'map', 'tone' => 'bg-secondary-soft text-secondary'],
                ['value' => 'PIRT', 'label' => 'Pangan Aman', 'icon' => 'verified', 'tone' => 'bg-primary-soft text-primary'],
                ['value' => number_format($umkmCount), 'label' => 'Pelaku Usaha', 'icon' => 'storefront', 'tone' => 'bg-secondary-soft text-secondary'],
            ] as $stat)
                <div class="rounded-3xl border border-outline-variant bg-white p-5 text-center shadow-lift transition hover:-translate-y-1 md:p-6">
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
    @if ($showFeaturedSection)
    <section class="mx-auto max-w-container px-4 py-16 md:px-6">
        <div class="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <div class="flex max-w-2xl gap-5">
                @if ($featuredSection?->image_url)
                    <img src="{{ $featuredSection->image_url }}" alt="{{ $featuredSection->image_alt ?? $featuredTitle }}" class="hidden h-24 w-32 rounded-2xl object-cover shadow-soft md:block">
                @endif
                <div>
                    <p class="eyebrow text-[11px] font-600 text-secondary">{{ $featuredSubtitle }}</p>
                    <h2 class="font-display mt-2 text-3xl font-700 text-ink md:text-4xl">{{ $featuredTitle }}</h2>
                    <p class="mt-3 leading-7 text-on-surface-variant">{{ $featuredContent }}</p>
                </div>
            </div>
            <a href="{{ $featuredButtonUrl }}" class="inline-flex items-center gap-2 self-start rounded-full bg-primary px-5 py-2.5 font-600 text-white transition-colors hover:bg-primary-container md:self-auto">
                {{ $featuredButtonText }}
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
    @endif

    {{-- Potensi lokal --}}
    @if ($showRegionSection)
    <section class="mx-auto max-w-container px-4 pb-20 md:px-6">
        <div class="relative overflow-hidden rounded-3xl bg-primary p-8 text-white md:p-12">
            <div class="relative grid gap-10 md:grid-cols-[1.2fr_1fr] md:items-center">
                <div>
                    <p class="eyebrow text-[11px] font-600 text-accent">{{ $regionSubtitle }}</p>
                    <h2 class="font-display mt-2 text-3xl font-700 md:text-4xl">{{ $regionTitle }}</h2>
                    <p class="mt-4 max-w-2xl leading-8 text-white/85">{{ $regionContent }}</p>
                    <a href="{{ $regionButtonUrl }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-accent px-5 py-2.5 font-600 text-ink transition-colors hover:bg-white">
                        {{ $regionButtonText }}
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                </div>
                @if ($regionSection?->image_url)
                    <img src="{{ $regionSection->image_url }}" alt="{{ $regionSection->image_alt ?? $regionTitle }}" class="aspect-[4/3] w-full rounded-2xl object-cover shadow-lift">
                @else
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ([
                            ['Kuliner', 'restaurant'],
                            ['Kemasan Aman', 'inventory'],
                            ['Minuman', 'local_cafe'],
                            ['Olahan Pangan', 'bakery_dining'],
                        ] as $item)
                            <div class="rounded-2xl border border-white/15 bg-white/10 p-5 transition hover:-translate-y-1 hover:bg-white/20">
                                <span class="material-symbols-outlined text-[26px] text-accent">{{ $item[1] }}</span>
                                <p class="mt-2 font-display text-lg font-600">{{ $item[0] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif
@endsection
