<header class="sticky top-0 z-40 border-b border-outline-variant/70 bg-surface/95 px-4 py-4 backdrop-blur md:px-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="eyebrow text-[10px] font-600 text-secondary">Panel Pengelola</p>
            <h1 class="font-display text-xl font-600 text-primary">@yield('page-title', 'Admin')</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 rounded-full border border-outline-variant px-4 py-2 text-sm font-600 text-primary transition-colors hover:bg-surface-container">
                <span class="material-symbols-outlined text-[18px]">public</span>
                <span class="hidden sm:inline">Lihat Website</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-600 text-surface transition-colors hover:bg-primary-container" type="submit">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
