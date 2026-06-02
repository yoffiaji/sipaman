@extends('layouts.admin')
@section('title', 'Edit Jenis Barang')
@section('page-title', 'Edit Jenis Barang')
@section('content')
<form action="{{ route('panel.jenis-barang.update', $jenisBarang) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @include('admin.jenis-barang._form', ['method' => 'PUT'])
</form>
@endsection
