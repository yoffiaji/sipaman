@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ([
            ['Produk', $stats['produk'] ?? 0, 'inventory_2', 'bg-primary-soft text-primary'],
            ['Terverifikasi', $stats['terverifikasi'] ?? 0, 'verified', 'bg-emerald-50 text-emerald-700'],
            ['Belum Verifikasi', $stats['belum_terverifikasi'] ?? 0, 'pending', 'bg-amber-50 text-amber-700'],
            ['UMKM', $stats['umkm'] ?? 0, 'storefront', 'bg-secondary-soft text-secondary'],
            ['Hampir Expired', $stats['hampir_expired'] ?? 0, 'schedule', 'bg-red-50 text-red-700'],
        ] as [$label, $value, $icon, $tone])
            <div class="rounded-2xl border border-outline-variant/70 bg-white p-5 shadow-soft transition hover:-translate-y-0.5 hover:shadow-lift">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $tone }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                </span>
                <p class="eyebrow mt-4 text-[10px] font-600 text-on-surface-variant">{{ $label }}</p>
                <p class="font-display mt-1 text-3xl font-700 text-primary">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-outline-variant/70 bg-white p-6 shadow-soft">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">upload_file</span>
                <h2 class="font-display text-xl font-600 text-primary">Ringkasan Import</h2>
            </div>
            @if ($stats['import_terakhir'] ?? null)
                <div class="mt-4 rounded-xl bg-surface-container-low p-4">
                    <p class="text-on-surface-variant">Import terakhir</p>
                    <p class="mt-0.5 font-600 text-on-surface">{{ $stats['import_terakhir']->nama_file }}</p>
                    <div class="mt-3 flex gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-600 text-emerald-700">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                            {{ $stats['import_terakhir']->jumlah_berhasil }} berhasil
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-600 text-red-700">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                            {{ $stats['import_terakhir']->jumlah_gagal }} gagal
                        </span>
                    </div>
                </div>
            @else
                <p class="mt-4 text-on-surface-variant">Belum ada import data.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-outline-variant/70 bg-white p-6 shadow-soft">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">inventory</span>
                <h2 class="font-display text-xl font-600 text-primary">Produk Terbaru</h2>
            </div>
            <div class="mt-4 space-y-2.5">
                @forelse ($stats['produk_terbaru'] ?? [] as $produk)
                    <a href="{{ route('panel.products.show', $produk) }}" class="group flex items-center gap-3 rounded-xl border border-outline-variant/60 p-3.5 transition-colors hover:border-primary/30 hover:bg-surface-container-low">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-soft text-primary">
                            <span class="material-symbols-outlined text-[18px]">package_2</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-600 text-on-surface">{{ $produk->nama_branding }}</p>
                            <p class="truncate text-sm text-on-surface-variant">{{ $produk->no_sppirt }}</p>
                        </div>
                        <span class="material-symbols-outlined text-[18px] text-outline transition-transform group-hover:translate-x-0.5">chevron_right</span>
                    </a>
                @empty
                    <p class="text-sm text-on-surface-variant">Belum ada produk.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
