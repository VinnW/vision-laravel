{{--
    Products Section
    - Slider kartu full-width (kartu terpotong di tepi layar), kartu tengah paling besar
    - Kartu berbingkai biru muda, rounded besar
    - Judul, deskripsi, panah kiri/kanan, dan tombol "Pelajari Lebih" di bawah slider
    - Logika slider (rel, go, prev, next, panelStyle, swipe, keyboard) tetap sama
    - Scroll margin menggunakan pixel agar posisi navigasi header lebih presisi
--}}

@php
    $detailUrl = \Illuminate\Support\Facades\Route::has('products.detail')
        ? route('products.detail')
        : url('/produk');

    // Data dari config/products.php: hanya ambil yang dibutuhkan slider.
    // Link menuju halaman detail dan langsung scroll ke produk yang dipilih (#slug).
    $products = collect(config('products', []))
        ->map(fn ($p) => [
            'name'        => $p['name'],
            'description' => $p['description'],
            'image'       => asset($p['image']),
            'link'        => $detailUrl . '#' . $p['slug'],
        ])
        ->values()
        ->all();
@endphp

<style>
    /* Jarak scroll untuk desktop */
    #products {
        scroll-margin-top: 55px;
    }

    /* Jarak scroll untuk tablet dan mobile */
    @media (max-width: 1023px) {
        #products {
            scroll-margin-top: 90px;
        }
    }
</style>

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
            this.jumpTimer = setTimeout(() => {
                this.jumping = [];
            }, 50);
        },

        prev() {
            this.go(this.active - 1);
        },

        next() {
            this.go(this.active + 1);
        },

        // Semua card berukuran 300x400, diposisikan absolute di tengah track.
        // Perbedaan ukuran hanya lewat scale().
        panelStyle(i) {
            const off = this.rel(i, this.active);
            const abs = Math.abs(off);
            const scale = abs === 0 ? 1 : abs === 1 ? 0.9 : 0.87;
            const x = off === 0 ? 0 : Math.sign(off) * (abs === 1 ? 300 : 580);
            const isJumping = this.jumping.includes(i);
            const hidden = isJumping || abs > 2;
            const opacity = hidden ? 0 : 1;
            const z = abs === 0 ? 20 : abs === 1 ? 10 : 0;
            const ease = '0.55s cubic-bezier(0.22, 1, 0.36, 1)';

            const transition = isJumping
                ? 'none'
                : `transform ${ease}, opacity 0.4s ease`;

            return `left:50%; top:50%; width:300px; height:400px; opacity:${opacity}; z-index:${z}; transform-origin:50% 50%; transform:translate3d(calc(-50% + ${x}px), -50%, 0) scale(${scale}); transition:${transition}; will-change:transform, opacity; backface-visibility:hidden;`;
        },
    }"
    @keydown.left.window="prev()"
    @keydown.right.window="next()"
    id="products"
    class="relative overflow-hidden bg-white pb-16 pt-28 lg:pb-20 lg:pt-32"
>
    {{-- Pola hexagon dekoratif (kiri bawah) --}}
    <svg
        class="pointer-events-none absolute -left-10 bottom-0 hidden h-80 w-[30rem] text-slate-200/80 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to right, black 20%, transparent 95%); mask-image: linear-gradient(to right, black 20%, transparent 95%);"
    >
        <defs>
            <pattern id="products-hex-bl" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>

        <rect width="100%" height="100%" fill="url(#products-hex-bl)" />
    </svg>

    {{-- Pola hexagon dekoratif (kanan bawah) --}}
    <svg
        class="pointer-events-none absolute -right-10 bottom-0 hidden h-80 w-[30rem] text-slate-200/80 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 95%); mask-image: linear-gradient(to left, black 20%, transparent 95%);"
    >
        <defs>
            <pattern id="products-hex-br" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>

        <rect width="100%" height="100%" fill="url(#products-hex-br)" />
    </svg>

    <div class="relative">

        {{-- Slider track: full-width, card diposisikan absolute --}}
        <div
            class="relative mx-auto w-full select-none"
            style="height: 350px;"
            @touchstart.passive="touchX = $event.changedTouches[0].clientX"
            @touchend.passive="
                if (touchX !== null) {
                    const dx = $event.changedTouches[0].clientX - touchX;

                    if (Math.abs(dx) > 40) {
                        dx < 0 ? next() : prev();
                    }

                    touchX = null;
                }
            "
        >
            <template x-for="(item, i) in items" :key="i">
                <button
                    type="button"
                    @click="go(i)"
                    :style="panelStyle(i)"
                    class="absolute overflow-hidden rounded-[2rem] border-4 border-[#6BB0FF] bg-slate-200 shadow-[0_20px_40px_-20px_rgba(30,64,175,0.35)]"
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

                    {{-- Placeholder abu-abu --}}
                    <template x-if="!item.image">
                        <div class="flex h-full w-full items-center justify-center bg-slate-200">
                            <svg
                                class="h-8 w-8 text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
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
        <div class="relative mx-auto mt-8 max-w-3xl px-6 text-center">

            <div class="flex items-start justify-between gap-3 sm:gap-6">

                {{-- Prev --}}
                <button
                    @click="prev()"
                    type="button"
                    aria-label="Produk sebelumnya"
                    class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-md border-2 border-[#10127A] text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>

                <div class="min-w-0 flex-1">

                    {{-- Judul --}}
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
                                class="col-start-1 row-start-1 text-2xl font-extrabold uppercase tracking-tight text-[#1F4E9E] sm:text-3xl"
                                x-text="item.name"
                            ></h2>
                        </template>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mx-auto mt-3 grid max-w-md">
                        <template x-for="(item, i) in items" :key="'desc-' + i">
                            <p
                                x-show="active === i"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition duration-150 ease-in"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="col-start-1 row-start-1 text-xs font-semibold leading-snug text-[#1B2F8F] sm:text-[13px]"
                                x-text="item.description"
                            ></p>
                        </template>
                    </div>

                    {{-- Menuju halaman detail produk yang sedang aktif --}}
                    <a
                        :href="items[active].link"
                        class="mt-4 inline-block rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-9 py-2 text-xs font-medium text-white shadow-[0_10px_22px_-10px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_26px_-10px_rgba(27,52,120,0.8)]"
                    >
                        Pelajari Lebih
                    </a>

                </div>

                {{-- Next --}}
                <button
                    @click="next()"
                    type="button"
                    aria-label="Produk berikutnya"
                    class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-md border-2 border-[#10127A] text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </button>

            </div>
        </div>
    </div>
</section>