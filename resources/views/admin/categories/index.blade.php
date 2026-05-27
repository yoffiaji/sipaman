@extends('layouts.admin')

@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between gap-4">
            <div>
                <h2 class="font-display text-xl font-bold">Kategori / Jenis Barang</h2>
                <p class="mt-1 text-slate-600">Kelola kategori olahan dan jenis produk PIRT.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Tambah</a>
        </div>
    </div>
@endsection
