@extends('layouts.admin')

@section('title', 'Kelola UMKM')
@section('page-title', 'Kelola UMKM')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between gap-4">
            <div>
                <h2 class="font-display text-xl font-bold">Pelaku Usaha</h2>
                <p class="mt-1 text-slate-600">Kelola akun dan data toko pelaku usaha.</p>
            </div>
            <a href="{{ route('admin.umkm.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Tambah UMKM</a>
        </div>
    </div>
@endsection
