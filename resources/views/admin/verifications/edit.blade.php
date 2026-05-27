@extends('layouts.admin')
@section('title', 'Edit Verifikasi')
@section('page-title', 'Edit Verifikasi')
@section('content')
<div class="space-y-6">
    @if ($errors->any()) <x-alert type="danger"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert> @endif
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div>
                <h2 class="font-display text-2xl font-bold">{{ $produk->nama_branding }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $produk->no_sppirt }} · {{ $produk->nama_pelaku_usaha }}</p>
                <p class="mt-1 text-sm text-slate-500">Wilayah: {{ $produk->kecamatan?->nama_kecamatan ?? $produk->wilayah ?? '-' }}</p>
            </div>
            @if($produk->is_verified)<x-badge-status status="terverifikasi">Terverifikasi</x-badge-status>@else<x-badge-status status="belum_terverifikasi">Belum Terverifikasi</x-badge-status>@endif
        </div>
        @if($produk->commitmentStatus)
            @php($cs = $produk->commitmentStatus)
            <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                <p class="font-semibold">Data dari Import Excel Status Komitmen</p>
                <p class="mt-1">NIB: {{ $cs->nib ?? '-' }} · Terdaftar: {{ $cs->tanggal_terdaftar?->format('d/m/Y') ?? '-' }}</p>
                <p>Status File Excel: <span class="font-semibold">{{ $cs->status_pemenuhan_komitmen ?? '-' }}</span></p>
            </div>
        @endif
    </div>

    <form action="{{ route('admin.verifications.update', $produk) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <h3 class="font-display text-lg font-bold">Form Verifikasi Manual</h3>
        <p class="mt-1 text-sm text-slate-500">Status komitmen otomatis terpenuhi hanya jika semua 4 syarat tercentang.</p>
        @php
            $v = $produk->verifikasi;
            $checkboxes = [
                ['name'=>'verifikasi_produk','label'=>'Verifikasi Produk','desc'=>'Produk sudah diperiksa dan sesuai standar.'],
                ['name'=>'verifikasi_label','label'=>'Verifikasi Label','desc'=>'Label kemasan sudah sesuai ketentuan PIRT.'],
                ['name'=>'pkp','label'=>'PKP','desc'=>'Pelaku usaha sudah mengikuti penyuluhan keamanan pangan.'],
                ['name'=>'cppob_pemeriksaan_sarana','label'=>'CPPOB / Pemeriksaan Sarana','desc'=>'Sarana produksi sudah diperiksa dan memenuhi standar.'],
            ];
        @endphp
        <div class="mt-6 space-y-4">
            @foreach($checkboxes as $cb)
                @php($checked = (bool) old($cb['name'], $v?->{$cb['name']} ?? false))
                <label class="flex cursor-pointer items-start gap-4 rounded-xl border border-slate-200 p-4 transition-colors hover:bg-slate-50 has-[:checked]:border-emerald-300 has-[:checked]:bg-emerald-50">
                    <input type="hidden" name="{{ $cb['name'] }}" value="0">
                    <input type="checkbox" name="{{ $cb['name'] }}" value="1" @checked($checked) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <div><p class="font-semibold text-slate-900">{{ $cb['label'] }}</p><p class="mt-0.5 text-sm text-slate-500">{{ $cb['desc'] }}</p></div>
                </label>
            @endforeach
        </div>
        <div class="mt-4"><label class="block text-sm font-semibold text-slate-700">Catatan</label><textarea name="catatan" rows="3" maxlength="1000" class="mt-1 w-full rounded-lg border-slate-300">{{ old('catatan', $v?->catatan ?? '') }}</textarea></div>
        <div class="mt-6 flex gap-3"><button class="rounded-lg bg-slate-900 px-6 py-2.5 font-semibold text-white">Simpan Verifikasi</button><a href="{{ route('admin.verifications.index') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 font-semibold text-slate-700">Batal</a></div>
    </form>
    @if($v?->verifikator)
        <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-600 shadow-sm"><p class="font-semibold text-slate-800">Riwayat Verifikasi Terakhir</p><p class="mt-1">Diverifikasi oleh: <span class="font-semibold">{{ $v->verifikator->nama }}</span></p><p>Diperbarui: {{ $v->updated_at->format('d/m/Y H:i') }}</p></div>
    @endif
</div>
@endsection
