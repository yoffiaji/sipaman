<footer class="relative z-[1] mt-auto bg-primary text-surface">
    <div class="mx-auto max-w-container px-4 md:px-6">
        <div class="grid grid-cols-1 gap-10 border-b border-surface/10 py-14 md:grid-cols-[1.4fr_1fr_1fr_1.1fr]">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent/20 text-accent">
                        <span class="material-symbols-outlined text-[22px]">eco</span>
                    </span>
                    <h2 class="font-display text-xl font-600">Karanganyar Portal</h2>
                </div>
                <p class="max-w-xs leading-7 text-surface/70">
                    Platform resmi direktori produk PIRT dan UMKM Kabupaten Karanganyar.
                </p>
            </div>

            <div>
                <h3 class="eyebrow mb-4 text-[11px] font-600 text-accent">Navigasi</h3>
                <ul class="space-y-2.5 text-surface/75">
                    <li><a class="transition-colors hover:text-accent" href="{{ route('home') }}">Home</a></li>
                    <li><a class="transition-colors hover:text-accent" href="{{ route('products.index') }}">Produk</a></li>
                    <li><a class="transition-colors hover:text-accent" href="{{ route('umkm.index') }}">UMKM</a></li>
                    <li><a class="transition-colors hover:text-accent" href="{{ auth()->check() ? route(auth()->user()->hasRole('user') ? 'user.dashboard' : 'admin.dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard' : 'Login' }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="eyebrow mb-4 text-[11px] font-600 text-accent">Layanan</h3>
                <ul class="space-y-2.5 text-surface/75">
                    <li><a class="transition-colors hover:text-accent" href="{{ route('products.index') }}">Katalog PIRT</a></li>
                    <li><a class="transition-colors hover:text-accent" href="{{ auth()->check() ? route(auth()->user()->hasRole('user') ? 'user.dashboard' : 'admin.dashboard') : route('login') }}">Dashboard</a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <h3 class="eyebrow mb-1 text-[11px] font-600 text-accent">Alamat Kantor</h3>
                <p class="flex gap-2 leading-7 text-surface/75">
                    <span class="material-symbols-outlined mt-0.5 text-[18px] text-accent">place</span>
                    Jl. Lawu No. 385, Karanganyar, Jawa Tengah 57711
                </p>
                <p class="flex gap-2 text-sm text-surface/55">
                    <span class="material-symbols-outlined text-[18px] text-accent">schedule</span>
                    Senin &ndash; Jumat, 08.00 &ndash; 16.00 WIB
                </p>
            </div>
        </div>

        <div class="flex flex-col justify-between gap-3 py-6 text-sm text-surface/55 md:flex-row">
            <p>&copy; {{ date('Y') }} Pemerintah Kabupaten Karanganyar.</p>
            <p class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-accent">verified</span>
                Verified by DISKOMINFO
            </p>
        </div>
    </div>
</footer>
