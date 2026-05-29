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
            Admin hanya mengubah isi konten. Layout, section key, route, dan struktur halaman tetap dikunci oleh sistem.
        </x-alert>

        @forelse($contents as $content)
            <form action="{{ route('admin.landing-page.update', $content) }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div class="grid gap-6 lg:grid-cols-[1fr_260px]">
                    <div>
                        <div class="flex flex-col justify-between gap-3 md:flex-row md:items-start">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $content->section_key }}</p>
                                <h2 class="font-display mt-1 text-lg font-bold text-slate-900">{{ str($content->section_key)->replace('_', ' ')->title() }}</h2>
                            </div>
                            <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $content->is_active)) class="rounded border-slate-300 text-slate-900">
                                Tampilkan section
                            </label>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Judul</label>
                                <input name="judul" value="{{ old('judul', $content->judul) }}" class="mt-1 w-full rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Subjudul</label>
                                <input name="subjudul" value="{{ old('subjudul', $content->subjudul) }}" class="mt-1 w-full rounded-lg border-slate-300">
                            </div>
                        </div>

                        <label class="mt-4 block text-sm font-semibold text-slate-700">Konten / Deskripsi</label>
                        <textarea name="konten" rows="1" data-auto-resize class="scrollbar-none mt-1 min-h-[7rem] w-full overflow-hidden rounded-lg border-slate-300">{{ old('konten', $content->konten) }}</textarea>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Teks Tombol</label>
                                <input name="button_text" value="{{ old('button_text', $content->button_text) }}" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Contoh: Lihat Produk">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Link Tombol</label>
                                <input name="button_url" value="{{ old('button_url', $content->button_url) }}" class="mt-1 w-full rounded-lg border-slate-300" placeholder="/products atau https://...">
                                <p class="mt-1 text-xs text-slate-500">Gunakan link yang diawali http://, https://, /, atau #.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Gambar Section</label>
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                                <p class="mt-1 text-xs text-slate-500">Format JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Alt Gambar</label>
                                <input name="image_alt" value="{{ old('image_alt', $content->image_alt) }}" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Deskripsi singkat gambar">
                            </div>
                        </div>

                        <button class="mt-5 rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white">Simpan Konten</button>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-700">Preview Gambar</p>
                        @if ($content->image_url)
                            <img src="{{ $content->image_url }}" alt="{{ $content->image_alt ?? $content->judul }}" class="mt-2 aspect-[4/3] w-full rounded-xl border border-slate-200 object-cover">
                            <label class="mt-3 flex items-center gap-2 text-sm font-semibold text-red-700">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-red-600">
                                Hapus gambar saat disimpan
                            </label>
                        @else
                            <div class="mt-2 flex aspect-[4/3] w-full items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-center text-sm text-slate-500">
                                Belum ada gambar
                            </div>
                        @endif

                        <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                            <p class="font-semibold text-slate-800">Terakhir diedit</p>
                            <p class="mt-1">{{ $content->updatedBy?->nama ?? '-' }}</p>
                            <p>{{ $content->updated_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </form>
        @empty
            <x-alert type="info">Belum ada konten landing page di database. Jalankan seeder default agar admin tinggal mengedit section yang sudah disediakan.</x-alert>
        @endforelse
    </div>
@endsection
