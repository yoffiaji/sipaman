@extends('layouts.public')
@section('title', $produk->nama_branding . ' | Karanganyar Portal')
@section('content')
<section class="mx-auto max-w-container px-4 pt-8 md:px-6">
    <nav class="flex items-center gap-1.5 text-sm text-on-surface-variant" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('products.index') }}" class="transition-colors hover:text-primary">Katalog</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="font-600 text-primary">{{ $produk->nama_branding }}</span>
    </nav>
</section>

<section class="mx-auto grid max-w-container gap-10 px-4 py-10 md:grid-cols-[1fr_1fr] md:px-6">
    <div class="overflow-hidden rounded-3xl border border-outline-variant/70 bg-surface-container shadow-soft">
        <img class="aspect-[4/3] h-full w-full object-cover" src="{{ $produk->gambarUtama?->gambar_url ?? 'https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $produk->nama_branding }}">
    </div>
    <div class="space-y-6">
        <x-badge-status status="terverifikasi">Terverifikasi</x-badge-status>
        <div>
            <h1 class="font-display text-4xl font-600 leading-tight text-primary md:text-5xl">{{ $produk->nama_branding }}</h1>
            <p class="mt-4 leading-8 text-on-surface-variant">{{ $produk->deskripsi ?? $produk->jenis_pangan ?? 'Produk PIRT terverifikasi dari Karanganyar.' }}</p>
        </div>
        <dl class="grid gap-px overflow-hidden rounded-2xl border border-outline-variant/70 bg-outline-variant/70 sm:grid-cols-2">
            @foreach ([
                ['Kecamatan/Wilayah', $produk->kecamatan?->nama_kecamatan ?? $produk->wilayah ?? '-', 'location_on'],
                ['Pelaku Usaha', $produk->nama_pelaku_usaha, 'storefront'],
                ['Harga', $produk->harga ? 'Rp ' . number_format($produk->harga, 0, ',', '.') : 'Belum tersedia', 'sell'],
                ['No SPPIRT', $produk->no_sppirt, 'badge'],
            ] as $row)
                <div class="bg-surface p-5">
                    <dt class="eyebrow flex items-center gap-1.5 text-[10px] font-600 text-secondary">
                        <span class="material-symbols-outlined text-[15px]">{{ $row[2] }}</span>
                        {{ $row[0] }}
                    </dt>
                    <dd class="mt-1.5 font-600 text-primary">{{ $row[1] }}</dd>
                </div>
            @endforeach
        </dl>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-600 text-surface transition-colors hover:bg-primary-container">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali ke katalog
        </a>
    </div>
</section>
@endsection
