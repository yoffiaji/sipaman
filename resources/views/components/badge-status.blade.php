@props([
    'status' => 'info',
])

@php
    $classes = [
        'verified'            => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
        'terverifikasi'       => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
        'terpenuhi'           => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
        'pending'             => 'bg-secondary-soft text-secondary ring-secondary/25',
        'proses'              => 'bg-secondary-soft text-secondary ring-secondary/25',
        'proses_verifikasi'   => 'bg-secondary-soft text-secondary ring-secondary/25',
        'belum_terverifikasi' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'belum'               => 'bg-amber-50 text-amber-800 ring-amber-600/20',
        'rejected'            => 'bg-red-50 text-red-800 ring-red-600/20',
        'ditolak'             => 'bg-red-50 text-red-800 ring-red-600/20',
        'expired'             => 'bg-red-50 text-red-800 ring-red-600/20',
        'info'                => 'bg-primary-soft text-primary ring-primary/20',
    ][$status] ?? 'bg-surface-container text-on-surface-variant ring-outline/25';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-600 uppercase tracking-wide ring-1 {$classes}"]) }}>
    {{ $slot->isEmpty() ? str_replace('_', ' ', ucfirst($status)) : $slot }}
</span>
