@extends('layouts.admin')

@section('title', 'Tambah UMKM')
@section('page-title', 'Tambah UMKM')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-xl font-bold">Tambah Pelaku Usaha</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <input class="rounded-lg border-slate-300" placeholder="Nama pemilik">
            <input class="rounded-lg border-slate-300" placeholder="Nama toko">
        </div>
    </div>
@endsection
