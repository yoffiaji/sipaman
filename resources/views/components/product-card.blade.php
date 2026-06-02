@props([
    'name',
    'district',
    'price' => null,
    'description' => null,
    'image' => null,
    'status' => 'terverifikasi',
    'href' => '#',
])

<article {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-2xl border border-outline-variant/70 bg-surface shadow-soft transition-all duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-lift']) }}>
    <a href="{{ $href }}" class="block">
        <div class="relative aspect-[4/3] overflow-hidden bg-surface-container">
            <img
                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                src="{{ $image ?? 'https://images.unsplash.com/photo-1606914501449-5a96b6ce24ca?auto=format&fit=crop&w=900&q=80' }}"
                alt="{{ $name }}"
                loading="lazy"
                decoding="async"
            >
            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-ink/40 to-transparent"></div>
            <div class="absolute left-3 top-3">
                <x-badge-status :status="$status">{{ $status === 'terverifikasi' ? 'Terverifikasi' : 'Belum Verifikasi' }}</x-badge-status>
            </div>
        </div>
    </a>

    <div class="space-y-2.5 p-5">
        <p class="eyebrow flex items-center gap-1 text-[10px] font-600 text-secondary">
            <span class="material-symbols-outlined text-[14px]">location_on</span>
            Kec. {{ $district }}
        </p>
        <h3 class="font-display text-xl font-600 leading-snug text-primary transition-colors group-hover:text-secondary">{{ $name }}</h3>
        @if ($description)
            <p class="line-clamp-2 text-sm leading-6 text-on-surface-variant">{{ $description }}</p>
        @endif
        <div class="flex items-center justify-between gap-3 border-t border-outline-variant/60 pt-3">
            <p class="font-display text-lg font-600 text-primary">{{ $price ?? 'NA' }}</p>
            <a href="{{ $href }}" class="inline-flex items-center gap-1 text-sm font-600 text-secondary transition-colors hover:text-primary">
                Detail
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
            </a>
        </div>
    </div>
</article>
