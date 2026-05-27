@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between gap-4">
            <div>
                <h2 class="font-display text-xl font-bold">Akun User</h2>
                <p class="mt-1 text-slate-600">Admin mengelola user pelaku usaha. Super admin mengelola admin.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Tambah User</a>
        </div>
    </div>
@endsection
