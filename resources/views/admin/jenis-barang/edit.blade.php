@extends('layouts.admin')
@section('title', 'Edit Jenis Barang')
@section('page-title', 'Edit Jenis Barang')
@section('content')
<form action="{{ route('admin.jenis-barang.update', $jenisBarang) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf @method('PUT')
    <label class="text-sm font-semibold">Nama Jenis</label>
    <input name="nama_jenis" value="{{ old('nama_jenis', $jenisBarang->nama_jenis) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    @error('nama_jenis')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
    <div class="mt-5 flex gap-3"><button class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button><a href="{{ route('admin.jenis-barang.index') }}" class="rounded-lg border px-4 py-2 font-semibold">Batal</a></div>
</form>
@endsection
