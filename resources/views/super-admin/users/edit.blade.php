@extends('layouts.admin')
@section('title', 'Atur Credential Akun')
@section('page-title', 'Atur Credential Akun')
@section('content')
@if ($errors->any())
    <x-alert type="danger" class="mb-5">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </x-alert>
@endif

<x-alert type="info" class="mb-5">
    Nama, identifier login, dan role dikunci agar identitas akun tetap konsisten. Pelaku usaha memakai NIB, admin memakai email. Yang dapat diubah hanya password dan status akun.
</x-alert>

<form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @include('super-admin.users._form', ['method' => 'PUT'])
</form>

@if (($user->role?->nama_role ?? null) === 'admin')
    <form
        action="{{ route('super-admin.users.destroy', $user) }}"
        method="POST"
        class="mt-4"
        data-confirm="Hapus admin ini?"
    >
        @csrf
        @method('DELETE')
        <button class="rounded-lg border border-red-200 px-4 py-2 font-semibold text-red-700 hover:bg-red-50">Hapus Admin</button>
    </form>
@else
    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
        Akun pelaku usaha tidak bisa dihapus. Gunakan status nonaktif atau kunci jika akses perlu dihentikan.
    </p>
@endif
@endsection
