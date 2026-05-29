@extends('layouts.admin')
@section('title', 'Tambah Admin')
@section('page-title', 'Tambah Admin')
@section('content')
@if ($errors->any())
    <x-alert type="danger" class="mb-5">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </x-alert>
@endif

<x-alert type="info" class="mb-5">
    Halaman ini hanya untuk membuat akun admin. Akun pelaku usaha dibuat otomatis dari import/verifikasi PIRT dan login memakai NIB.
</x-alert>

<form action="{{ route('super-admin.users.store') }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @include('super-admin.users._form', ['method' => 'POST'])
</form>
@endsection
