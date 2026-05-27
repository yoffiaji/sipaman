@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
    <div class="space-y-5">
        @if ($errors->any())
            <x-alert type="danger"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>
        @endif
        <form action="{{ route('admin.products.update', $produk) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-display mb-5 text-xl font-bold">Edit Produk</h2>
            @include('admin.products._form', ['method' => 'PUT'])
        </form>
    </div>
@endsection
