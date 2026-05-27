@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-xl font-bold">Buat Credential User</h2>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <input class="rounded-lg border-slate-300" placeholder="Nama">
            <input class="rounded-lg border-slate-300" placeholder="Email">
            <input class="rounded-lg border-slate-300" placeholder="Password awal">
        </div>
    </div>
@endsection
