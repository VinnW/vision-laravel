{{--
    Products / Layanan Section
    - "Coverflow" style slider: 5 panel terlihat, panel tengah lebih besar &
      terangkat, sisanya mengecil ke tepi — sesuai wireframe.
    - Tailwind CSS + Alpine.js untuk state slide aktif & navigasi prev/next.
    - Ukuran tiap panel di-set lewat :style (inline CSS), BUKAN lewat class
      Tailwind yang dirakit di JS — supaya tidak bergantung pada bagaimana
      Tailwind men-scan class dinamis (rawan tidak ter-generate).
    - $products dikirim dari controller (name, description, image, link).
      'image' opsional — kalau kosong akan tampil sebagai blok placeholder
      abu-abu (tanpa perlu koneksi internet), persis seperti wireframe.
--}}
@php
    $products ??= collect(range(1, 7))->map(fn ($i) => [
        'name'        => "Nama Produk Layanan {$i}",
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
        'image'       => null,
        'link'        => '#',
    ])->all();
@endphp

<section
    x-data="{
        active: 2,
        items: @json($products),
        get total() { return this.items.length },
        prev() { this.active = (this.active - 1 + this.total) % this.total },
        next() { this.active = (this.active + 1) % this.total },
        slot(offset) { return this.items[(this.active + offset + this.total) % this.total] },
        panelStyle(offset) {
            const abs = Math.abs(offset);
            const size = abs === 0 ? { w: 288, h: 544 } : abs === 1 ? { w: 240, h: 480 } : { w: 192, h: 432 };
            const opacity = abs === 0 ? 1 : abs === 1 ? 0.9 : 0.6;
            const z = abs === 0 ? 20 : abs === 1 ? 10 : 0;
            return `width:${size.w}px; height:${size.h}px; opacity:${opacity}; z-index:${z};`;
        },
    }"
    class="relative overflow-hidden bg-white py-20 lg:py-28"
>
    {{-- Decorative mesh-gradient orbs, kept subtle so it doesn't compete with the images --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute left-1/4 -top-20 h-80 w-80 rounded-full bg-indigo-200/20 blur-3xl"></div>
        <div class="absolute right-1/4 -bottom-20 h-80 w-80 rounded-full bg-[#F2A93B]/10 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

        {{-- Slider track --}}
        <div class="flex items-end justify-center gap-3 sm:gap-4 md:gap-5">
            <template x-for="offset in [-2, -1, 0, 1, 2]" :key="offset">
                <button
                    type="button"
                    @click="active = (active + offset + total) % total"
                    :style="panelStyle(offset)"
                    class="group relative shrink-0 overflow-hidden rounded-[2rem] bg-slate-200 shadow-2xl shadow-slate-900/20 transition-all duration-500 ease-out"
                    :aria-label="slot(offset).name"
                >
                    {{-- Real image, kalau tersedia --}}
                    <template x-if="slot(offset).image">
                        <img
                            :src="slot(offset).image"
                            :alt="slot(offset).name"
                            class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                        />
                    </template>

                    {{-- Placeholder abu-abu, dummy, tanpa perlu internet --}}
                    <template x-if="!slot(offset).image">
                        <div class="flex h-full w-full items-center justify-center bg-slate-200">
                            <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="m21 15-5-5-9 9" />
                            </svg>
                        </div>
                    </template>

                    <div
                        class="pointer-events-none absolute inset-0 bg-slate-900/0 transition-colors duration-300"
                        :class="offset === 0 ? '' : 'group-hover:bg-slate-900/10'"
                    ></div>
                </button>
            </template>
        </div>

        {{-- Caption + controls --}}
        <div class="relative mt-14 text-center">
            <h2
                class="text-2xl font-extrabold uppercase tracking-tight text-slate-900 sm:text-3xl"
                x-text="items[active].name"
            ></h2>

            <div class="mt-6 flex items-center justify-center gap-4 sm:gap-6">
                <button
                    @click="prev()"
                    type="button"
                    aria-label="Produk sebelumnya"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-slate-900 text-slate-900 transition-all duration-200 hover:border-[#F2A93B] hover:bg-[#F2A93B] hover:text-white"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>

                <p
                    class="max-w-xl text-[15px] font-semibold leading-relaxed text-slate-700"
                    x-text="items[active].description"
                ></p>

                <button
                    @click="next()"
                    type="button"
                    aria-label="Produk berikutnya"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border-2 border-slate-900 text-slate-900 transition-all duration-200 hover:border-[#F2A93B] hover:bg-[#F2A93B] hover:text-white"
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