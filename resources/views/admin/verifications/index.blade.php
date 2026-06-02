@extends('layouts.admin')
@section('title', 'Verifikasi Produk')
@section('page-title', 'Verifikasi Produk')
@section('content')
<div class="space-y-6">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    @if ($errors->any()) <x-alert type="danger"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert> @endif
    @if (session('import_failures') && count(session('import_failures')) > 0)
        <x-alert type="warning"><div class="font-semibold">Sebagian baris gagal dibaca. Contoh maksimal 5 baris:</div><ul class="mt-2 list-disc pl-5">@foreach(session('import_failures') as $failure)<li>Baris {{ $failure['baris'] ?? '-' }}: {{ $failure['errors'][0] ?? 'Data tidak valid.' }}</li>@endforeach</ul></x-alert>
    @endif

    <div class="grid gap-4 md:grid-cols-4">
        @foreach ([['Semua', $stats['total']], ['Terverifikasi', $stats['terverifikasi']], ['Proses', $stats['proses']], ['Belum', $stats['belum']]] as [$label, $value])
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($value) }}</p></div>
        @endforeach
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-xl font-bold">Import Status Pemenuhan Komitmen</h2>
        <p class="mt-1 text-slate-600">Upload Excel status komitmen untuk sinkron otomatis ke verifikasi produk.</p>
        <p class="mt-1 text-sm font-semibold text-amber-700">Status verifikasi hanya diperbarui melalui import Excel Status Pemenuhan Komitmen agar sesuai dengan data sumber resmi.</p>
        <p class="mt-1 text-sm text-slate-500">Format yang didukung: .xls, .xlsx, dan .csv. Maksimal 10 MB.</p>
        @if ($lastImport)
            <p class="mt-2 text-sm text-slate-500">Import terakhir: <span class="font-semibold">{{ $lastImport->nama_file }}</span> oleh {{ $lastImport->user?->nama ?? '-' }}</p>
        @endif
        <form action="{{ route('panel.verifications.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 flex flex-col gap-3 md:flex-row md:items-center">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:font-semibold">
            <button class="rounded-lg bg-blue-700 px-5 py-2 font-semibold text-white">Import Status</button>
        </form>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div><h2 class="font-display text-xl font-bold">Daftar Verifikasi</h2><p class="mt-1 text-slate-600">Status ditampilkan read-only dan mengikuti hasil import Excel.</p></div>
            <div class="flex flex-wrap gap-2 text-sm font-semibold">
                @foreach(['semua'=>'Semua','terverifikasi'=>'Terverifikasi','proses'=>'Proses','belum'=>'Belum'] as $key => $label)
                    <a href="{{ route('panel.verifications.index', ['tab' => $key]) }}" class="rounded-lg border px-3 py-2 {{ $tab === $key ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <form method="GET" action="{{ route('panel.verifications.index') }}" class="mt-5 flex gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari produk / No SPPIRT..." class="w-full rounded-lg border-slate-300">
            <button class="rounded-lg border border-slate-300 px-4 py-2 font-semibold">Cari</button>
        </form>
        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr><th class="px-4 py-3">Produk</th><th class="px-4 py-3">Syarat</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Verifikator</th><th class="px-4 py-3"></th></tr></thead>
                <tbody>
                    @forelse($products as $product)
                        @php($v = $product->verifikasi)
                        <tr class="border-t align-top">
                            <td class="px-4 py-3"><div class="font-semibold">{{ $product->nama_branding }}</div><div class="text-xs text-slate-500">{{ $product->no_sppirt }}</div></td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                Produk: {{ $v?->verifikasi_produk ? '✓' : '×' }} · Label: {{ $v?->verifikasi_label ? '✓' : '×' }} · PKP: {{ $v?->pkp ? '✓' : '×' }} · CPPOB: {{ $v?->cppob_pemeriksaan_sarana ? '✓' : '×' }}
                            </td>
                            <td class="px-4 py-3">@if($product->is_verified)<x-badge-status status="terverifikasi">Terverifikasi</x-badge-status>@elseif($v)<x-badge-status status="proses">Proses</x-badge-status>@else<x-badge-status status="belum_terverifikasi">Belum</x-badge-status>@endif</td>
                            <td class="px-4 py-3">{{ $v?->verifikator?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-right"><a class="font-semibold text-blue-700" href="{{ route('panel.verifications.show', $product) }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $products->links() }}</div>
    </div>
</div>
@endsection
