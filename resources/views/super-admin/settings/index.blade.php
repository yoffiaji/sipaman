@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

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
            System Settings dipakai untuk konfigurasi global seperti nama aplikasi, kontak, alamat, dan teks footer. Keterangan fungsi pengaturan hanya bantuan untuk admin, bukan konten publik. Jangan simpan password, token, API key, atau secret di halaman ini.
        </x-alert>

        @forelse($settings as $setting)
            <form action="{{ route('super-admin.settings.update', $setting) }}" method="POST" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div class="flex flex-col justify-between gap-3 md:flex-row md:items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $setting->key }}</p>
                        <h2 class="font-display mt-1 text-lg font-bold text-slate-900">{{ str($setting->key)->replace('_', ' ')->title() }}</h2>
                        @if ($setting->deskripsi)
                            <p class="mt-1 text-sm text-slate-600">{{ $setting->deskripsi }}</p>
                        @endif
                    </div>
                </div>

                <label class="mt-5 block text-sm font-semibold text-slate-700">Nilai</label>
                <textarea name="value" rows="3" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Isi nilai pengaturan global">{{ old('value', $setting->value) }}</textarea>

                <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <p class="text-sm font-semibold text-slate-700">Keterangan Fungsi Pengaturan</p>
                    <p class="mt-1 text-sm text-slate-600">{{ $setting->deskripsi ?: 'Belum ada keterangan.' }}</p>
                </div>

                <button class="mt-4 rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Simpan Pengaturan</button>
            </form>
        @empty
            <x-alert type="info">Belum ada pengaturan sistem. Jalankan seeder default agar super admin dapat mengelola konfigurasi global.</x-alert>
        @endforelse
    </div>
@endsection
