@extends('layouts.admin')
@section('title', 'Tambah Jenis Barang')
@section('page-title', 'Tambah Jenis Barang')
@section('content')
<form action="{{ route('admin.jenis-barang.store') }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @include('admin.jenis-barang._form', ['method' => 'POST'])
</form>
@endsection
