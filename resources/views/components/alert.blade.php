@props([
    'type' => 'info',
])

@php
    $config = [
        'success' => ['border-emerald-300 bg-emerald-50 text-emerald-900', 'check_circle'],
        'warning' => ['border-amber-300 bg-amber-50 text-amber-900', 'warning'],
        'danger'  => ['border-red-300 bg-red-50 text-red-900', 'error'],
        'info'    => ['border-primary/25 bg-primary-soft text-primary', 'info'],
    ][$type] ?? ['border-outline-variant bg-surface-container text-on-surface', 'info'];
    [$classes, $icon] = $config;
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-2.5 rounded-xl border px-4 py-3 text-sm leading-6 {$classes}"]) }}>
    <span class="material-symbols-outlined mt-0.5 text-[18px]">{{ $icon }}</span>
    <div>{{ $slot }}</div>
</div>
