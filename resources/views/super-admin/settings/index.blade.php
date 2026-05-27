@extends('layouts.admin')
@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('content')
<div class="space-y-5">
@if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
@forelse($settings as $setting)
<form action="{{ route('super-admin.settings.update', $setting) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT')<p class="text-xs font-bold uppercase text-slate-500">{{ $setting->key }}</p><label class="mt-4 block text-sm font-semibold">Value</label><textarea name="value" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('value', $setting->value) }}</textarea><label class="mt-4 block text-sm font-semibold">Deskripsi</label><input name="deskripsi" value="{{ old('deskripsi', $setting->deskripsi) }}" class="mt-1 w-full rounded-lg border-slate-300"><button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button></form>
@empty<x-alert type="info">Belum ada pengaturan sistem.</x-alert>@endforelse
</div>
@endsection
