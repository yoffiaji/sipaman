@extends('layouts.auth')

@section('title', 'Registrasi Dinonaktifkan | Karanganyar Portal')

@section('content')
    <section class="mx-auto max-w-2xl px-4 py-20 text-center">
        <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-secondary-soft text-secondary">
            <span class="material-symbols-outlined text-[32px]">lock_person</span>
        </span>
        <h1 class="font-display mt-6 text-3xl font-600 text-primary">Registrasi Dinonaktifkan</h1>
        <x-alert type="warning" class="mt-6 text-left">
            Registrasi mandiri dinonaktifkan. Akun pelaku usaha dibuat oleh admin PIRT berdasarkan data resmi.
        </x-alert>

        <a href="{{ route('login') }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 font-600 text-surface transition-colors hover:bg-primary-container">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali ke Login
        </a>
    </section>
@endsection
