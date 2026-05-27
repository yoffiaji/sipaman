@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('content')
    <div class="space-y-5">
        <x-alert type="info">Produk baru sebaiknya berasal dari import Excel PIRT. Form ini tetap disediakan untuk input manual oleh admin.</x-alert>
        @if ($errors->any())
            <x-alert type="danger"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>
        @endif
        <form action="{{ route('admin.products.store') }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-display mb-5 text-xl font-bold">Form Produk</h2>
            @include('admin.products._form', ['method' => 'POST'])
        </form>
    </div>
@endsection
