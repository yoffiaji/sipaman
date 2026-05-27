<section class="relative overflow-hidden">
    {{-- soft gradient canvas instead of a heavy dark block --}}
    <div class="absolute inset-0 bg-gradient-to-br from-primary-soft via-surface to-secondary-soft"></div>
    <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-primary/15 blur-3xl"></div>
    <div class="absolute -right-16 top-20 h-72 w-72 rounded-full bg-accent/25 blur-3xl"></div>

    <div class="relative mx-auto max-w-container px-4 py-20 md:px-6 md:py-28">
        <div class="grid items-center gap-10 md:grid-cols-[1.15fr_0.85fr]">
            <div class="max-w-2xl">
                <span class="rise inline-flex items-center gap-2 rounded-full border border-primary/20 bg-surface/70 px-4 py-1.5 text-xs font-600 text-primary backdrop-blur">
                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                    <span class="eyebrow text-[10px]">Pemerintah Kabupaten Karanganyar</span>
                </span>
                <h1 class="rise font-display mt-7 text-4xl font-700 leading-[1.07] text-ink md:text-6xl" style="animation-delay:.08s">
                    Pusat Informasi &amp; Direktori
                    <span class="relative whitespace-nowrap text-primary">
                        Produk Lokal
                        <svg class="absolute -bottom-2 left-0 w-full" height="10" viewBox="0 0 200 10" fill="none" preserveAspectRatio="none">
                            <path d="M2 7c40-6 156-6 196 0" stroke="#fbbf24" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </span>
                    Unggulan
                </h1>
                <p class="rise mt-6 max-w-xl text-lg leading-8 text-on-surface-variant" style="animation-delay:.16s">
                    Temukan produk PIRT, pelaku usaha, dan potensi UMKM dari Karanganyar dalam satu katalog yang mudah dicari.
                </p>

                <form action="{{ route('products.index') }}" method="GET" class="rise mt-8 grid max-w-2xl gap-2.5 rounded-3xl border border-outline-variant bg-white p-2.5 shadow-lift md:grid-cols-[1fr_200px_auto]" style="animation-delay:.24s">
                    <label class="flex items-center gap-2 rounded-2xl bg-surface-container-low px-3.5 transition focus-within:bg-primary-soft">
                        <span class="material-symbols-outlined text-primary">search</span>
                        <input class="w-full border-0 bg-transparent py-3.5 text-on-surface placeholder:text-on-surface-variant focus:ring-0" name="search" placeholder="Cari nama produk..." type="text">
                    </label>
                    <label class="flex items-center gap-2 rounded-2xl bg-surface-container-low px-3.5 transition focus-within:bg-primary-soft">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                        <select class="w-full border-0 bg-transparent py-3.5 text-on-surface focus:ring-0" name="kecamatan">
                            <option value="">Semua Kecamatan</option>
                            <option value="karanganyar">Karanganyar</option>
                            <option value="tawangmangu">Tawangmangu</option>
                            <option value="ngargoyoso">Ngargoyoso</option>
                        </select>
                    </label>
                    <button class="inline-flex items-center justify-center gap-1.5 rounded-2xl bg-primary px-6 py-3.5 font-600 text-white transition-colors hover:bg-primary-container" type="submit">
                        Cari
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </form>
            </div>

            {{-- floating illustrative card stack --}}
            <div class="relative hidden md:block">
                <div class="floaty overflow-hidden rounded-3xl border border-outline-variant bg-white shadow-lift">
                    <img class="aspect-[4/3] w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLrzNuod1W88GW69ugOmUrt4j1qCELF1klFhhnZudL1lYxg7X8aNouYhd3KHcLFglPJpk5iwCgdEHvioVvqFTO9efu5B_vKjmdm_lk0mkMGnoyi1dLubfUEp17j29SthqXA6MEru3ynC4b9KLGfsh_faubjcV-7L-PbHhi-T3wakAOYN43-dcI3FwN4Tf68Y7-soCzSS4OVUdLOB6oH6gp_7QXOEz0P6p-NlF1WfkOv4tSOcN1rA_t9HjL6YYCCapVOJ8I-DTY2E8" alt="Lanskap Karanganyar">
                </div>
                <div class="absolute -bottom-5 -left-6 flex items-center gap-3 rounded-2xl border border-outline-variant bg-white p-3.5 shadow-lift">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <span class="material-symbols-outlined">verified</span>
                    </span>
                    <div>
                        <p class="font-display text-sm font-700 text-ink">Produk Terverifikasi</p>
                        <p class="text-xs text-on-surface-variant">Lulus uji legalitas PIRT</p>
                    </div>
                </div>
                <div class="absolute -right-5 -top-5 flex items-center gap-2 rounded-2xl border border-outline-variant bg-accent px-3.5 py-2.5 shadow-lift">
                    <span class="material-symbols-outlined text-[20px] text-ink">storefront</span>
                    <p class="font-display text-sm font-700 text-ink">UMKM Karanganyar</p>
                </div>
            </div>
        </div>
    </div>
</section>
