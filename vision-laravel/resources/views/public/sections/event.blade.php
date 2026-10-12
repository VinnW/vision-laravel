{{--
    Event Section
    - Dua panel berdampingan (Coming Up Next & Vision Update), gambar full-bleed
    - Pudar putih di atas (area header) dan di bawah, pola hexagon biru muda
    - Judul biru tua besar, paragraf kecil biru tua, tombol pill navy
    - Query database & slider gambar (4 detik) tidak diubah
--}}

@php
    /*
    |--------------------------------------------------------------------------
    | Data Vision Update
    |--------------------------------------------------------------------------
    */

    $visionUpdates = \Illuminate\Support\Facades\DB::table('vision_update_section')
        ->whereNotNull('image_url')
        ->where('image_url', '!=', '')
        ->orderByDesc('id')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Data Coming Up Next
    |--------------------------------------------------------------------------
    */

    $comingImages = \Illuminate\Support\Facades\DB::table('coming_up_next_section')
        ->whereNotNull('image_url')
        ->where('image_url', '!=', '')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Helper: ubah path gambar dari database menjadi URL
    |--------------------------------------------------------------------------
    */

    $resolveSrc = function ($path) {
        $path = trim((string) $path);

        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    };


    /*
    |--------------------------------------------------------------------------
    | Konfigurasi kolom halaman
    |--------------------------------------------------------------------------
    */

    $columns ??= [
        [
            'tone'      => 'dark',
            'heading'   => "Coming Up\nNext",
            'paragraph' => 'Nantikan berbagai event, kegiatan, dan program menarik dari Vision. Temukan pengalaman baru, perluas koneksi, dan nikmati berbagai benefit bersama komunitas Vision.',
            'image'     => null,
            'action'    => [
                'label' => 'See Event',
                'route' => 'event.coming',
            ],
        ],

        [
            'tone'      => 'light',
            'heading'   => "Vision\nUpdate",
            'paragraph' => 'Dapatkan informasi terbaru seputar program, layanan, promo, dan berbagai benefit eksklusif untuk komunitas Vision. Tetap terhubung dan jangan lewatkan setiap update serta kesempatan menarik dari Vision.',
            'image'     => null,
            'action'    => [
                'label' => 'Article',
                'route' => 'event.update',
            ],
        ],
    ];
@endphp


<section id="event" class="grid grid-cols-1 bg-white lg:grid-cols-2">

    @foreach ($columns as $column)

        @php
            $isComingUp     = ($column['heading'] ?? '') === "Coming Up\nNext";
            $isVisionUpdate = ($column['heading'] ?? '') === "Vision\nUpdate";
            $isFirst        = $loop->first;
        @endphp


        {{-- =========================================================
             PANEL
             ========================================================= --}}

        <div
            @if ($isComingUp && $comingImages->isNotEmpty())

                x-data="{
                    active: 0,
                    total: {{ $comingImages->count() }}
                }"

                x-init="
                    setInterval(() => {
                        active = (active + 1) % total;
                    }, 4000);
                "

            @elseif ($isVisionUpdate && $visionUpdates->isNotEmpty())

                x-data="{
                    active: 0,
                    total: {{ $visionUpdates->count() }}
                }"

                x-init="
                    setInterval(() => {
                        active = (active + 1) % total;
                    }, 4000);
                "

            @elseif (!empty($column['image']))

                style="
                    background-image: url('{{ $column['image'] }}');
                    background-size: cover;
                    background-position: center;
                "

            @endif

            class="relative flex min-h-[36rem] flex-col justify-end overflow-hidden bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#E3F2FB] px-8 pb-14 pt-44 sm:px-12 lg:min-h-[42rem] lg:px-[4.5rem]"
        >


            {{-- =====================================================
                 BACKGROUND IMAGE
                 ===================================================== --}}

            @if ($isComingUp && $comingImages->isNotEmpty())

                <div class="pointer-events-none absolute inset-0">

                    @foreach ($comingImages as $index => $coming)

                        @php $src = $resolveSrc($coming->image_url ?? ''); @endphp

                        @if ($src !== '')
                            <img
                                src="{{ $src }}"
                                alt="{{ $coming->title ?? 'Coming Up Next' }}"
                                class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700"
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition-opacity duration-700"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                style="display: none;"
                            >
                        @endif

                    @endforeach

                </div>

            @elseif ($isVisionUpdate && $visionUpdates->isNotEmpty())

                <div class="pointer-events-none absolute inset-0">

                    @foreach ($visionUpdates as $index => $update)

                        @php $src = $resolveSrc($update->image_url ?? ''); @endphp

                        @if ($src !== '')
                            <img
                                src="{{ $src }}"
                                alt="{{ $update->title ?? 'Vision Update' }}"
                                class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700"
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition-opacity duration-700"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                style="display: none;"
                            >
                        @endif

                    @endforeach

                </div>

            @endif


            {{-- =====================================================
                 OVERLAY PUTIH
                 ===================================================== --}}

            {{-- Pudar putih di atas (area header) --}}
            <div
                class="pointer-events-none absolute inset-x-0 top-0 h-48 bg-linear-to-b from-white via-white/70 to-transparent"
                aria-hidden="true"
            ></div>

            {{-- Pudar putih di bawah (di belakang teks) --}}
            <div
                class="pointer-events-none absolute inset-x-0 bottom-0 h-3/5 bg-linear-to-t from-white/80 via-white/40 to-transparent"
                aria-hidden="true"
            ></div>

            {{-- Pudar putih di sisi dalam (menyatu antar panel) --}}
            <div
                class="pointer-events-none absolute inset-y-0 {{ $isFirst ? 'right-0 bg-linear-to-l' : 'left-0 bg-linear-to-r' }} hidden w-16 from-white/40 to-transparent lg:block"
                aria-hidden="true"
            ></div>


            {{-- =====================================================
                 POLA HEXAGON
                 ===================================================== --}}

            <svg
                class="pointer-events-none absolute bottom-0 {{ $isFirst ? 'left-0' : 'right-0' }} hidden h-96 w-full max-w-[34rem] text-sky-300/60 md:block"
                aria-hidden="true"
                style="-webkit-mask-image: linear-gradient({{ $isFirst ? 'to right' : 'to left' }}, black 30%, transparent 100%); mask-image: linear-gradient({{ $isFirst ? 'to right' : 'to left' }}, black 30%, transparent 100%);"
            >
                <defs>
                    <pattern id="event-hex-{{ $loop->index }}" width="56" height="98" patternUnits="userSpaceOnUse">
                        <path
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                            transform="scale(2)"
                        />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#event-hex-{{ $loop->index }})" />
            </svg>


            {{-- =====================================================
                 CONTENT
                 ===================================================== --}}

            <div class="relative z-10">

                {{-- HEADING --}}
                <h2 class="text-5xl font-extrabold uppercase leading-[1.08] tracking-tight text-[#1F4E9E] lg:text-[3.4rem]">
                    {!! nl2br(e($column['heading'])) !!}
                </h2>

                {{-- PARAGRAPH --}}
                <p class="mt-8 max-w-md text-xs font-medium leading-snug text-[#1B2F8F] sm:text-[13px]">
                    {{ $column['paragraph'] }}
                </p>

                {{-- CTA --}}
                <a
                    href="{{ Route::has($column['action']['route']) ? route($column['action']['route']) : '#' }}"
                    class="mt-10 inline-flex w-full items-center justify-center rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,52,120,0.85)] sm:w-auto sm:min-w-[7.5rem]"
                >
                    {{ $column['action']['label'] }}
                </a>

            </div>

        </div>

    @endforeach

</section>