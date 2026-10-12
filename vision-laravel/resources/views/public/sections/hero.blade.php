{{--
    Hero Section
    - Banner dari database menjadi gambar utama Hero (full-width, di belakang header transparan)
    - Judul dan subtitle tetap, rata kiri, warna biru tua, tanpa background
    - Overlay putih lembut di atas & kiri agar header dan teks terbaca
    - Slider otomatis setiap 5 detik
    - Tombol di bawah hero + panah "scroll" ke section berikutnya
--}}

@php
    if (!isset($slides) || empty($slides)) {
        $slides = [
            [
                'id' => 0,
                'image' => null,
            ],
        ];
    }
@endphp

<section
    x-data="{
        active: 0,
        total: {{ count($slides) }},
        timer: null
    }"

    x-init="
        if (total > 1) {
            timer = setInterval(() => {
                active = (active + 1) % total
            }, 5000)
        }
    "

    class="relative overflow-hidden bg-white pb-14"
>

    {{-- Dekorasi hexagon (kanan bawah) --}}
    <svg
        class="pointer-events-none absolute -right-10 bottom-0 hidden h-80 w-[42rem] text-sky-300/40 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 95%); mask-image: linear-gradient(to left, black 20%, transparent 95%);"
    >
        <defs>
            <pattern id="hero-hex-br" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#hero-hex-br)" />
    </svg>


    {{-- ========================================================= --}}
    {{-- HERO BANNER (full width) --}}
    {{-- ========================================================= --}}

    <div class="relative h-[480px] w-full overflow-hidden bg-slate-100 sm:h-[540px] lg:h-[520px]">

        {{-- BANNER SLIDES --}}
        @foreach ($slides as $index => $slide)

            <div
                x-show="active === {{ $index }}"

                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"

                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"

                class="absolute inset-0"

                @if ($index > 0)
                    style="display: none;"
                @endif
            >

                @if (!empty($slide['image']))

                    <img
                        src="{{ $slide['image'] }}"
                        alt="Banner {{ $index + 1 }}"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                        class="absolute inset-0 h-full w-full object-cover object-[70%_center]"
                    />

                @else

                    <div
                        class="absolute inset-0 flex items-center justify-center bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#FFF3E2]"
                    >
                        <svg
                            class="h-20 w-20 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="3" y="3" width="18" height="14" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="m21 15-5-5-9 9" />
                        </svg>
                    </div>

                @endif

            </div>

        @endforeach


        {{-- OVERLAY: pudar putih di atas (di belakang header) --}}
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-44 bg-linear-to-b from-white via-white/70 to-transparent"
            aria-hidden="true"
        ></div>

        {{-- OVERLAY: pudar putih di kiri (di belakang teks) --}}
        <div
            class="pointer-events-none absolute inset-y-0 left-0 w-full bg-linear-to-r from-white/70 via-white/25 to-transparent lg:w-3/5"
            aria-hidden="true"
        ></div>

        {{-- Pola hexagon garis (kiri atas) --}}
        <svg
            class="pointer-events-none absolute left-0 top-24 hidden h-72 w-[34rem] text-white/80 md:block"
            aria-hidden="true"
            style="-webkit-mask-image: linear-gradient(to right, black 10%, transparent 95%); mask-image: linear-gradient(to right, black 10%, transparent 95%);"
        >
            <defs>
                <pattern id="hero-hex-tl" width="56" height="98" patternUnits="userSpaceOnUse">
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                        transform="scale(2)"
                    />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-hex-tl)" />
        </svg>


        {{-- ===================================================== --}}
        {{-- STATIC TEXT (rata kiri, tanpa background) --}}
        {{-- ===================================================== --}}

        <div class="relative z-10 flex h-full items-center pt-20">

            <div class="mx-auto w-full max-w-7xl px-6 lg:px-10">

                <div class="max-w-xl sm:max-w-2xl lg:ml-4">

                    {{-- Static Headline --}}
                    <h1
                        class="text-4xl font-extrabold uppercase leading-[1.08] tracking-tight text-[#1F4E9E] sm:text-5xl lg:text-[3.4rem]"
                    >
                        Rasa Aman Saat<br>Menjelajah Dunia
                    </h1>

                    {{-- Static Subtitle --}}
                    <p
                        class="mt-6 max-w-md text-lg font-semibold leading-snug text-[#1B3F8F] sm:text-xl lg:text-[1.65rem]"
                    >
                        Dengan perlindungan Asuransi lengkap selama anda berada di luar negri
                    </p>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- SLIDE INDICATORS --}}
        {{-- ===================================================== --}}

        @if (count($slides) > 1)

            <div
                class="absolute bottom-6 left-0 right-0 z-20 flex items-center justify-center gap-2"
            >

                @foreach ($slides as $index => $slide)

                    <button
                        type="button"

                        @click="
                            active = {{ $index }};

                            clearInterval(timer);

                            timer = setInterval(() => {
                                active = (active + 1) % total
                            }, 5000)
                        "

                        :class="
                            active === {{ $index }}
                                ? 'w-6 bg-[#1E3A8A]'
                                : 'w-2 bg-white/80 hover:bg-white'
                        "

                        class="h-2 rounded-full shadow-sm transition-all duration-300"

                        :aria-current="active === {{ $index }}"

                        aria-label="Slide {{ $index + 1 }}"
                    ></button>

                @endforeach

            </div>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- ACTION BUTTONS --}}
    {{-- ========================================================= --}}

    <div class="relative z-30 mx-auto mt-10 max-w-7xl px-6 lg:px-10">

        <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">

            {{-- Products --}}
            <a
                href="{{ Route::has('products.detail') ? route('products.detail') : '#' }}"
                class="w-full rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-4 text-center text-sm font-medium text-white shadow-[0_16px_32px_-14px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_38px_-14px_rgba(27,52,120,0.8)] sm:w-auto sm:min-w-60"
            >
                Produk Layanan
            </a>

            {{-- Join Agent --}}
            <a
                href="{{ Route::has('join-agent') ? route('join-agent') : '#' }}"
                class="w-full rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-4 text-center text-sm font-medium text-white shadow-[0_16px_32px_-14px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_38px_-14px_rgba(27,52,120,0.8)] sm:w-auto sm:min-w-60"
            >
                Join Agent
            </a>

        </div>

        {{-- Teks + panah scroll --}}
        <a
            href="#about"
            class="mt-8 flex flex-col items-center gap-2 text-center text-sm text-[#1B3F8F] transition-opacity hover:opacity-80"
        >
            <span>Mengenal vision lebih dekat dan lengkap</span>

            <svg class="h-4 w-4 text-[#1E3A8A]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M2 5h20L12 21z" />
            </svg>
        </a>

    </div>

</section>