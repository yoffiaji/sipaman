@extends('layouts.admin')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('content')
<div class="space-y-5">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex justify-between gap-4"><div><h2 class="font-display text-xl font-bold">User Management</h2><p class="mt-1 text-slate-600">Super admin mengelola akun user dan admin.</p></div><a href="{{ route('super-admin.users.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Tambah User</a></div>
        <form method="GET" class="mt-5 grid gap-3 md:grid-cols-[1fr_180px_auto]"><input name="search" value="{{ request('search') }}" placeholder="Cari nama/email" class="rounded-lg border-slate-300"><select name="role" class="rounded-lg border-slate-300"><option value="">Semua Role</option>@foreach(['user','admin'] as $role)<option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>@endforeach</select><button class="rounded-lg border px-4 py-2 font-semibold">Filter</button></form>
        <table class="mt-5 w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Status</th><th></th></tr></thead><tbody>@forelse($users as $user)<tr class="border-t"><td class="px-4 py-3 font-semibold">{{ $user->nama }}</td><td class="px-4 py-3">{{ $user->email }}</td><td class="px-4 py-3">{{ $user->role?->nama_role }}</td><td class="px-4 py-3">{{ $user->status_akun }}</td><td class="px-4 py-3 text-right"><a class="font-semibold text-blue-700" href="{{ route('super-admin.users.edit', $user) }}">Edit</a></td></tr>@empty<tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada user.</td></tr>@endforelse</tbody></table>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</div>
@endsection
