@php
    $role = auth()->user()?->role?->nama_role;

    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard*'],
        ['label' => 'Produk', 'icon' => 'inventory_2', 'route' => 'admin.products.index', 'active' => 'admin.products.*'],
        ['label' => 'Jenis Barang', 'icon' => 'category', 'route' => 'admin.jenis-barang.index', 'active' => 'admin.jenis-barang.*'],
        ['label' => 'Verifikasi', 'icon' => 'verified', 'route' => 'admin.verifications.index', 'active' => 'admin.verifications.*'],
        ['label' => 'Landing Page', 'icon' => 'web', 'route' => 'admin.landing-page.index', 'active' => 'admin.landing-page.*'],
        ['label' => 'Log Aktivitas', 'icon' => 'history', 'route' => 'admin.logs.index', 'active' => 'admin.logs.*'],
    ];

    if ($role === 'super_admin') {
        $items[] = ['label' => 'Kelola User', 'icon' => 'group', 'route' => 'super-admin.users.index', 'active' => 'super-admin.users.*'];
        $items[] = ['label' => 'System Settings', 'icon' => 'settings', 'route' => 'super-admin.settings.index', 'active' => 'super-admin.settings.*'];
        $items[] = ['label' => 'Audit Trail', 'icon' => 'manage_search', 'route' => 'super-admin.audit-trails.index', 'active' => 'super-admin.audit-trails.*'];
    }
@endphp
<aside class="hidden border-r border-outline-variant/70 bg-primary text-surface lg:block">
    <div class="sticky top-0 flex h-screen flex-col">
        <div class="border-b border-surface/10 px-6 py-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent/20 text-accent">
                    <span class="material-symbols-outlined text-[20px]">landscape</span>
                </span>
                <span class="leading-tight">
                    <span class="block font-display text-lg font-600">Admin PIRT</span>
                    <span class="eyebrow block text-[9px] font-600 text-surface/55">Karanganyar Portal</span>
                </span>
            </a>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5" aria-label="Navigasi admin">
            @foreach($items as $item)
                @php($active = request()->routeIs($item['active']))
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-600 transition-colors {{ $active ? 'bg-accent text-primary shadow-soft' : 'text-surface/70 hover:bg-surface/10 hover:text-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="border-t border-surface/10 px-4 py-4">
            <div class="flex items-center gap-3 rounded-xl bg-surface/10 px-3.5 py-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent/25 text-sm font-700 text-accent">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-600 text-surface">{{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="eyebrow truncate text-[9px] font-600 text-surface/55">{{ str_replace('_', ' ', $role ?? 'admin') }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
