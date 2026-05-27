@extends('layouts.public')
@section('title', $nama . ' | Karanganyar Portal')
@section('content')
<section class="mx-auto max-w-container px-4 pt-10 md:px-6">
    <div class="relative overflow-hidden rounded-3xl border border-outline-variant bg-gradient-to-br from-primary-soft via-white to-secondary-soft p-8 md:p-10">
        <div class="absolute -right-10 -top-10 h-48 w-48 rounded-full bg-accent/25 blur-3xl"></div>
        <nav class="relative flex items-center gap-1.5 text-sm text-on-surface-variant" aria-label="Breadcrumb">
            <a href="{{ route('umkm.index') }}" class="transition-colors hover:text-primary">UMKM</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="font-600 text-primary">{{ $nama }}</span>
        </nav>
        <div class="relative mt-5 flex items-start gap-4">
            <span class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-primary text-white shadow-soft sm:flex">
                <span class="material-symbols-outlined text-[30px]">storefront</span>
            </span>
            <div>
                <x-badge-status status="info">Pelaku Usaha</x-badge-status>
                <h1 class="font-display mt-3 text-4xl font-700 text-ink md:text-5xl">{{ $nama }}</h1>
                <p class="mt-3 max-w-2xl leading-8 text-on-surface-variant">{{ $umkm->alamat ?? 'Pelaku usaha Karanganyar dengan produk PIRT terverifikasi.' }}</p>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-container px-4 py-14 md:px-6">
    <h2 class="font-display mb-8 text-3xl font-700 text-ink">Produk dari {{ $nama }}</h2>
    <div class="grid gap-6 md:grid-cols-3">
        @foreach($products as $product)
            <x-product-card
                :name="$product->nama_branding"
                :district="$product->kecamatan?->nama_kecamatan ?? $product->wilayah ?? 'Karanganyar'"
                :price="$product->harga ? 'Rp ' . number_format($product->harga, 0, ',', '.') : null"
                :description="$product->deskripsi ?? $product->jenis_pangan"
                :image="$product->gambarUtama?->gambar_url"
                :href="route('products.show', $product)"
            />
        @endforeach
    </div>
    <div class="mt-10">{{ $products->links() }}</div>
</section>
@endsection
