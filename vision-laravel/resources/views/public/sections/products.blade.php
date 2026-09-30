{{--
    Products / Layanan Section
    - "Coverflow" style slider: 5 panel terlihat, panel tengah lebih besar &
      terangkat, sisanya mengecil ke tepi — sesuai wireframe.
    - Tailwind CSS + Alpine.js untuk state slide aktif & navigasi prev/next.
    - Setiap produk adalah SATU card tetap (position: absolute) berukuran sama.
      Perbedaan ukuran dibuat lewat transform: scale() (bukan width/height),
      supaya animasi hanya memakai transform + opacity yang diproses GPU
      dan tidak memicu layout ulang -> tetap ringan walau diklik cepat.
    - Card yang "membungkus" dari ujung kiri ke ujung kanan (loop) dipindah
      tanpa transisi dan di-fade-in, supaya tidak terbang melintasi layar.
    - Ukuran/posisi di-set lewat :style (inline CSS), BUKAN lewat class
      Tailwind yang dirakit di JS — supaya tidak bergantung pada bagaimana
      Tailwind men-scan class dinamis (rawan tidak ter-generate).
    - Data produk dikirim ke Alpine lewat @js() (bukan @json) supaya tanda
      kutip di-escape dengan aman di dalam atribut x-data.
    - 'image' opsional — kalau kosong akan tampil sebagai blok placeholder
      abu-abu (tanpa perlu koneksi internet), persis seperti wireframe.
--}}
@php
    $products = [
        [
            'name' => 'Asuransi Kesehatan',
            'description' => 'Perlindungan kesehatan untuk membantu memberikan rasa aman dalam menghadapi kebutuhan medis dan biaya perawatan.',
            'image' => asset('storage/image_products/asuransi-kesehatan.jpg'),
            'link' => '#',
        ],

        [
            'name' => 'Asuransi Kendaraan',
            'description' => 'Perlindungan kendaraan dari berbagai risiko sehingga Anda dapat berkendara dengan lebih tenang.',
            'image' => asset('storage/image_products/asuransi-kendaraan.jpg'),
            'link' => '#',
        ],

        [
            'name' => 'Asuransi Jiwa',
            'description' => 'Perlindungan finansial bagi Anda dan keluarga untuk menghadapi berbagai risiko kehidupan.',
            'image' => asset('storage/image_products/asuransi-jiwa.jpg'),
            'link' => '#',
        ],

        [
            'name' => 'Asuransi Properti',
            'description' => 'Perlindungan untuk rumah dan aset properti dari berbagai risiko yang tidak terduga.',
            'image' => asset('storage/image_products/asuransi-properti.jpg'),
            'link' => '#',
        ],

        [
            'name' => 'Asuransi Perjalanan',
            'description' => 'Perlindungan perjalanan untuk membantu Anda menghadapi berbagai risiko selama bepergian.',
            'image' => asset('storage/image_products/asuransi-perjalanan.jpg'),
            'link' => '#',
        ],
    ];
@endphp

<section
    x-data="{
        active: 2,
        items: @js($products),
        jumping: [],
        jumpTimer: null,
        touchX: null,
        get total() { return this.items.length },

        // Jarak (offset) terpendek card i terhadap slide aktif a, mis. -2..2
        rel(i, a) {
            let d = (((i - a) % this.total) + this.total) % this.total;
            if (d > this.total / 2) d -= this.total;
            return d;
        },

        // Pindah ke slide baru. Card yang membungkus dari satu ujung ke ujung lain
        // ditandai 'jumping' agar dipindah tanpa transisi lalu di-fade-in.
        go(target) {
            const t = ((target % this.total) + this.total) % this.total;
            if (t === this.active) return;
            const step = this.rel(t, this.active);
            const wrapped = [];
            this.items.forEach((_, i) => {
                const expected = this.rel(i, this.active) - step;
                if (expected !== this.rel(i, t)) wrapped.push(i);
            });
            this.jumping = wrapped;
            this.active = t;
            clearTimeout(this.jumpTimer);
            this.jumpTimer = setTimeout(() => { this.jumping = [] }, 50);
        },
        prev() { this.go(this.active - 1) },
        next() { this.go(this.active + 1) },

        // Semua card berukuran 288x544. Perbedaan ukuran hanya lewat scale().
        panelStyle(i) {
            const off = this.rel(i, this.active);
            const abs = Math.abs(off);
            const scale = abs === 0 ? 1 : abs === 1 ? 0.88 : 0.79;
            const x = off === 0 ? 0 : Math.sign(off) * (abs === 1 ? 290 : 550);
            const isJumping = this.jumping.includes(i);
            const hidden = isJumping || abs > 2;
            const opacity = hidden ? 0 : (abs === 0 ? 1 : abs === 1 ? 0.9 : 0.6);
            const z = abs === 0 ? 20 : abs === 1 ? 10 : 0;
            const ease = '0.55s cubic-bezier(0.22, 1, 0.36, 1)';
            const transition = isJumping
                ? 'none'
                : `transform ${ease}, opacity 0.4s ease`;
            return `left:50%; bottom:0; width:288px; height:544px; opacity:${opacity}; z-index:${z}; transform-origin:50% 100%; transform:translate3d(calc(-50% + ${x}px), 0, 0) scale(${scale}); transition:${transition}; will-change:transform, opacity; backface-visibility:hidden;`;
        },
    }"
    @keydown.left.window="prev()"
    @keydown.right.window="next()"
    id="products" class="relative overflow-hidden bg-white py-20 lg:py-28"
>
    {{-- Decorative mesh-gradient orbs, kept subtle so it doesn't compete with the images --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute left-1/4 -top-20 h-80 w-80 rounded-full bg-indigo-200/20 blur-3xl"></div>
        <div class="absolute right-1/4 -bottom-20 h-80 w-80 rounded-full bg-[#F2A93B]/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

        {{-- Slider track: card diposisikan absolute, digeser lewat transform --}}
        <div
            class="relative mx-auto w-full select-none"
            style="height: 544px;"
            @touchstart.passive="touchX = $event.changedTouches[0].clientX"
            @touchend.passive="
                if (touchX !== null) {
                    const dx = $event.changedTouches[0].clientX - touchX;
                    if (Math.abs(dx) > 40) { dx < 0 ? next() : prev() }
                    touchX = null;
                }
            "
        >
            <template x-for="(item, i) in items" :key="i">
                <button
                    type="button"
                    @click="go(i)"
                    :style="panelStyle(i)"
                    class="absolute overflow-hidden rounded-[2rem] bg-slate-200 shadow-2xl shadow-slate-900/20"
                    :aria-label="item.name"
                >
                    {{-- Real image, kalau tersedia --}}
                    <template x-if="item.image">
                        <img
                            :src="item.image"
                            :alt="item.name"
                            decoding="async"
                            draggable="false"
                            class="h-full w-full object-cover"
                        />
                    </template>

                    {{-- Placeholder abu-abu, dummy, tanpa perlu internet --}}
                    <template x-if="!item.image">
                        <div class="flex h-full w-full items-center justify-center bg-slate-200">
                            <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="m21 15-5-5-9 9" />
                            </svg>
                        </div>
                    </template>
                </button>
            </template>
        </div>

        {{-- Caption + controls --}}
        <div class="relative mt-14 text-center">

            {{-- Judul: semua item ditumpuk di satu sel grid, yang aktif di-fade --}}
            <div class="grid">
                <template x-for="(item, i) in items" :key="'title-' + i">
                    <h2
                        x-show="active === i"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition duration-150 ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="col-start-1 row-start-1 text-2xl font-extrabold uppercase tracking-tight text-slate-900 sm:text-3xl"
                        x-text="item.name"
                    ></h2>
                </template>
            </div>

            <div class="mt-6 flex items-center justify-center gap-4 sm:gap-6">
                <button
                    @click="prev()"
                    type="button"
                    aria-label="Produk sebelumnya"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-slate-900 text-slate-900 transition-all duration-200 hover:border-[#F2A93B] hover:bg-[#F2A93B] hover:text-white active:scale-95"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>

                {{-- Deskripsi: sama, ditumpuk & di-fade --}}
                <div class="grid max-w-xl">
                    <template x-for="(item, i) in items" :key="'desc-' + i">
                        <p
                            x-show="active === i"
                            x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition duration-150 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="col-start-1 row-start-1 text-[15px] font-semibold leading-relaxed text-slate-700"
                            x-text="item.description"
                        ></p>
                    </template>
                </div>

                <button
                    @click="next()"
                    type="button"
                    aria-label="Produk berikutnya"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-slate-900 text-slate-900 transition-all duration-200 hover:border-[#F2A93B] hover:bg-[#F2A93B] hover:text-white active:scale-95"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </button>
            </div>

            <a
                :href="items[active].link"
                class="mt-5 inline-block text-sm font-semibold text-slate-400 underline decoration-slate-300 underline-offset-4 transition-colors duration-200 hover:text-[#F2A93B] hover:decoration-[#F2A93B]"
            >
                Pelajari selengkapnya
            </a>
        </div>
    </div>
</section>