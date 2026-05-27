@extends('layouts.admin')
@section('title', 'Jenis Barang')
@section('page-title', 'Jenis Barang')
@section('content')
<div class="space-y-5">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    @if ($errors->any()) <x-alert type="danger"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert> @endif
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between gap-4">
            <div><h2 class="font-display text-xl font-bold">Jenis Barang</h2><p class="mt-1 text-slate-600">Klasifikasi produk sesuai data PIRT.</p></div>
            <a href="{{ route('admin.jenis-barang.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Tambah</a>
        </div>
        <table class="mt-5 w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Produk</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>@forelse($jenisBarangs as $jenisBarang)<tr class="border-t"><td class="px-4 py-3 font-semibold">{{ $jenisBarang->nama_jenis }}</td><td class="px-4 py-3">{{ $jenisBarang->produks_count }}</td><td class="px-4 py-3 text-right"><a class="font-semibold text-blue-700" href="{{ route('admin.jenis-barang.edit', $jenisBarang) }}">Edit</a></td></tr>@empty<tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">Belum ada jenis barang.</td></tr>@endforelse</tbody>
        </table>
        <div class="mt-4">{{ $jenisBarangs->links() }}</div>
    </div>
</div>
@endsection
