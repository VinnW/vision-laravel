{{--
    Hero Section
    - Banner dari database menjadi gambar utama Hero
    - Container memiliki tinggi tetap 520px
    - Gambar memenuhi seluruh area container
    - Judul dan subtitle tetap, tidak berubah saat gambar berganti
    - Tidak ada background di belakang teks
    - Slider otomatis setiap 5 detik
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

    class="relative overflow-hidden bg-slate-50 pb-24 pt-14 lg:pb-32 lg:pt-20"
>

    {{-- Decorative background --}}
    <div
        class="pointer-events-none absolute inset-0 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute -left-24 -top-32 h-96 w-96 rounded-full bg-[#F2A93B]/20 blur-3xl"
        ></div>

        <div
            class="absolute -right-20 top-10 h-112 w-112 rounded-full bg-indigo-400/15 blur-3xl"
        ></div>

        <div
            class="absolute -bottom-24 left-1/3 h-80 w-80 rounded-full bg-sky-300/15 blur-3xl"
        ></div>
    </div>


    <div class="relative mx-auto max-w-7xl px-6 lg:px-10">

        {{-- ========================================================= --}}
        {{-- HERO CONTAINER --}}
        {{-- ========================================================= --}}

        <div
            class="relative min-h-[520px] overflow-hidden rounded-[2.5rem] border border-white/60 bg-slate-100 shadow-[0_30px_80px_-30px_rgba(30,41,59,0.25)]"
        >

            {{-- ===================================================== --}}
            {{-- BANNER SLIDES --}}
            {{-- ===================================================== --}}

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
                            class="absolute inset-0 h-full w-full object-cover"
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
                                <rect
                                    x="3"
                                    y="3"
                                    width="18"
                                    height="14"
                                    rx="2"
                                />

                                <circle
                                    cx="8.5"
                                    cy="8.5"
                                    r="1.5"
                                />

                                <path d="m21 15-5-5-9 9" />
                            </svg>
                        </div>

                    @endif

                </div>

            @endforeach


            {{-- ===================================================== --}}
            {{-- SOFT OVERLAY --}}
            {{-- ===================================================== --}}

            <div
                class="pointer-events-none absolute inset-0 bg-black/10"
                aria-hidden="true"
            ></div>


            {{-- ===================================================== --}}
            {{-- STATIC TEXT --}}
            {{-- ===================================================== --}}

            <div
                class="relative z-10 flex min-h-[520px] items-center justify-center px-6 text-center sm:px-10 lg:px-16"
            >

                <div class="max-w-3xl">

                    {{-- Static Headline --}}
                    <h1
                        class="text-4xl font-extrabold leading-tight text-white drop-shadow-[0_4px_12px_rgba(0,0,0,0.35)] sm:text-5xl lg:text-6xl"
                    >
                        Lorem Ipsum
                    </h1>

                    {{-- Static Subtitle --}}
                    <p
                        class="mt-5 text-lg leading-relaxed text-white drop-shadow-[0_3px_8px_rgba(0,0,0,0.35)] sm:text-xl"
                    >
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    </p>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- SLIDE INDICATORS --}}
            {{-- ===================================================== --}}

            @if (count($slides) > 1)

                <div
                    class="absolute bottom-7 left-0 right-0 z-20 flex items-center justify-center gap-2"
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
                                    ? 'w-6 bg-indigo-600'
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

        <div
            class="relative z-30 -mt-7 flex flex-col items-center justify-center gap-4 sm:flex-row"
        >

            {{-- Products --}}
                        <a
                href="{{ Route::has('products.detail') ? route('products.detail') : '#' }}"
                class="w-full rounded-full bg-white px-8 py-4 text-center text-sm font-semibold text-slate-700 shadow-[0_18px_40px_-15px_rgba(30,41,59,0.35)] ring-1 ring-slate-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_45px_-15px_rgba(30,41,59,0.4)] sm:w-auto sm:min-w-60"
            >
                Produk Layanan
            </a>


            {{-- Join Agent --}}
            <a
                href="{{ Route::has('join-agent') ? route('join-agent') : '#' }}"
                class="w-full rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-8 py-4 text-center text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_45px_-15px_rgba(232,130,60,0.65)] sm:w-auto sm:min-w-60"
            >
                Join Agent
            </a>

        </div>

    </div>

</section>