@extends('layouts.admin')
@section('title', 'Detail Verifikasi')
@section('page-title', 'Detail Verifikasi')
@section('content')
@php
    $v = $produk->verifikasi;
    $checks = [
        'Verifikasi Produk' => $v?->verifikasi_produk,
        'Verifikasi Label' => $v?->verifikasi_label,
        'PKP' => $v?->pkp,
        'CPPOB / Pemeriksaan Sarana' => $v?->cppob_pemeriksaan_sarana,
    ];
@endphp

<div class="space-y-6">
    <x-alert type="info">
        Status verifikasi hanya diperbarui melalui import Excel Status Pemenuhan Komitmen agar sesuai dengan data sumber resmi.
    </x-alert>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div>
                <h2 class="font-display text-2xl font-bold">{{ $produk->nama_branding }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $produk->no_sppirt }} - {{ $produk->nama_pelaku_usaha }}</p>
                <p class="mt-1 text-sm text-slate-500">Wilayah: {{ $produk->kecamatan?->nama_kecamatan ?? $produk->wilayah ?? '-' }}</p>
            </div>
            @if($produk->is_verified)
                <x-badge-status status="terverifikasi">Terverifikasi</x-badge-status>
            @else
                <x-badge-status status="belum_terverifikasi">Belum Terverifikasi</x-badge-status>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-bold">Status Syarat</h3>
            <div class="mt-5 space-y-3">
                @foreach($checks as $label => $passed)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
                        <span class="font-semibold text-slate-800">{{ $label }}</span>
                        @if($passed)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Lulus</span>
                        @else
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Belum / Tidak</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-5 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-800">Catatan</p>
                <p class="mt-1">{{ $v?->catatan ?: '-' }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-display text-lg font-bold">Data Import Terakhir</h3>
            @if($produk->commitmentStatus)
                @php($cs = $produk->commitmentStatus)
                <dl class="mt-5 grid gap-3 text-sm">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="font-semibold text-slate-500">NIB</dt>
                        <dd class="mt-1 text-slate-900">{{ $cs->nib ?? '-' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="font-semibold text-slate-500">Tanggal Terdaftar</dt>
                        <dd class="mt-1 text-slate-900">{{ $cs->tanggal_terdaftar?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="font-semibold text-slate-500">Status Pemenuhan Komitmen</dt>
                        <dd class="mt-1 font-semibold text-slate-900">{{ $cs->status_pemenuhan_komitmen ?? '-' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-500">Belum ada data Status Pemenuhan Komitmen dari import Excel.</p>
            @endif

            @if($v?->verifikator)
                <div class="mt-5 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">Riwayat Terakhir</p>
                    <p class="mt-1">Diperbarui oleh: <span class="font-semibold">{{ $v->verifikator->nama }}</span></p>
                    <p>Diperbarui: {{ $v->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.verifications.index') }}" class="inline-flex rounded-lg border border-slate-300 px-5 py-2.5 font-semibold text-slate-700">Kembali</a>
</div>
@endsection
