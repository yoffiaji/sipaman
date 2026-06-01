@extends('layouts.admin')

@section('title', 'Landing Page')
@section('page-title', 'Landing Page')

@section('content')
    <div class="space-y-5">
        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="danger">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <x-alert type="info">
            Landing Page dipakai khusus untuk mengatur konten halaman depan. Layout, urutan bagian, route, dan struktur tampilan dikunci oleh sistem.
        </x-alert>

        <div class="grid gap-5">
            @forelse($contents as $content)
                @php
                    $meta = $sectionMeta[$content->section_key] ?? [
                        'label' => 'Bagian Halaman Depan',
                        'description' => 'Bagian konten halaman depan website.',
                        'allows_image' => false,
                    ];
                @endphp

                <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-5 md:flex-row md:items-start">
                        <div class="space-y-4">
                            <div>
                                <h2 class="font-display text-xl font-bold text-slate-900">{{ $meta['label'] }}</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $meta['description'] }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                                <span class="inline-flex w-fit items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $content->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    <span class="material-symbols-outlined text-[16px]">{{ $content->is_active ? 'visibility' : 'visibility_off' }}</span>
                                    {{ $content->is_active ? 'Tampil di website' : 'Disembunyikan' }}
                                </span>

                                @if ($content->updatedBy || $content->updated_at)
                                    <span class="inline-flex flex-wrap items-center gap-x-2 gap-y-1 rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                        <span>Terakhir diedit</span>
                                        <span class="text-slate-800">{{ $content->updatedBy?->nama ?? '-' }}</span>
                                        <span>{{ $content->updated_at?->format('d M Y H:i') ?? '-' }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('admin.landing-page.edit', $content) }}" class="inline-flex w-fit items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Edit Bagian
                        </a>
                    </div>
                </article>
            @empty
                <x-alert type="info">Belum ada konten landing page di database. Jalankan seeder default agar admin tinggal mengedit bagian yang sudah disediakan.</x-alert>
            @endforelse
        </div>
    </div>
@endsection
