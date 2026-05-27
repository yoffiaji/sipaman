@extends('layouts.admin')
@section('title', 'Landing Page')
@section('page-title', 'Landing Page')
@section('content')
<div class="space-y-5">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    @forelse($contents as $content)
        <form action="{{ route('admin.landing-page.update', $content) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf @method('PUT')
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $content->section_key }}</p>
            <label class="mt-4 block text-sm font-semibold">Judul</label>
            <input name="judul" value="{{ old('judul', $content->judul) }}" class="mt-1 w-full rounded-lg border-slate-300">
            <label class="mt-4 block text-sm font-semibold">Konten</label>
            <textarea name="konten" rows="4" class="mt-1 w-full rounded-lg border-slate-300">{{ old('konten', $content->konten) }}</textarea>
            <button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button>
        </form>
    @empty
        <x-alert type="info">Belum ada konten landing page di database.</x-alert>
    @endforelse
</div>
@endsection
