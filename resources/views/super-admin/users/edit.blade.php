@extends('layouts.admin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('content')
@if ($errors->any()) <x-alert type="danger" class="mb-5"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert> @endif
<form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">@include('super-admin.users._form', ['method'=>'PUT'])</form>
<form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="mt-4" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-4 py-2 font-semibold text-red-700 hover:bg-red-50">Hapus User</button></form>
@endsection
