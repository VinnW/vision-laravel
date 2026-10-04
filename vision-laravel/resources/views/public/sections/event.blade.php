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

    $comingImages = DB::table('coming_up_next_section')
    ->whereNotNull('image_url')
    ->where('image_url', '!=', '')
    ->get();


    /*
    |--------------------------------------------------------------------------
    | Konfigurasi kolom halaman
    |--------------------------------------------------------------------------
    */

    $columns ??= [
        [
            'tone'      => 'dark',
            'heading'   => "Coming Up\nNext",
            'paragraph' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
            'image'     => null,
            'action'    => [
                'label' => 'Daftar Sekarang',
                'route' => 'event.coming',
            ],
        ],

        [
            'tone'      => 'light',
            'heading'   => "Vision\nUpdate",
            'paragraph' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
            'image'     => null,
            'action'    => [
                'label' => 'Baca Selengkapnya',
                'route' => 'event.update',
            ],
        ],
    ];
@endphp


<section id="event" class="grid grid-cols-1 lg:grid-cols-2">

    @foreach ($columns as $column)

        @php
            $isDark = ($column['tone'] ?? 'dark') === 'dark';

            $isComingUp = ($column['heading'] ?? '') === "Coming Up\nNext";

            $isVisionUpdate = ($column['heading'] ?? '') === "Vision\nUpdate";

            /*
            |--------------------------------------------------------------------------
            | Overlay untuk fallback image
            |--------------------------------------------------------------------------
            */

            $overlay = $isDark
                ? 'rgba(15,23,42,.68), rgba(15,23,42,.68)'
                : 'rgba(255,255,255,.8), rgba(255,255,255,.8)';
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
                    background-image: linear-gradient({{ $overlay }}), url('{{ $column['image'] }}');
                    background-size: cover;
                    background-position: center;
                "

            @endif

            class="
                relative
                flex
                min-h-152
                flex-col
                justify-between
                overflow-hidden
                px-8
                py-14
                sm:px-12
                lg:min-h-176
                lg:px-16
                lg:py-20

                {{ $isDark
                    ? 'bg-slate-500 text-white'
                    : 'bg-slate-200 text-slate-900'
                }}
            "
        >


            {{-- =====================================================
                 BACKGROUND IMAGE
                 ===================================================== --}}

            @if ($isComingUp && $comingImages->isNotEmpty())

                {{-- -------------------------------------------------
                     COMING UP NEXT IMAGES
                     ------------------------------------------------- --}}

                <div class="pointer-events-none absolute inset-0">

                    @foreach ($comingImages as $index => $coming)

                        @php
                            $path = trim((string) ($coming->image_url ?? ''));

                            if ($path === '') {
                                $src = '';
                            } elseif (
                                preg_match('#^https?://#', $path) ||
                                str_starts_with($path, '/')
                            ) {
                                $src = $path;
                            } elseif (str_starts_with($path, 'storage/')) {
                                $src = asset($path);
                            } else {
                                $src = asset('storage/' . ltrim($path, '/'));
                            }
                        @endphp


                        @if ($src !== '')

                            <img
                                src="{{ $src }}"
                                alt="{{ $coming->title ?? 'Coming Up Next' }}"
                                class="
                                    absolute
                                    inset-0
                                    h-full
                                    w-full
                                    object-cover
                                    transition-opacity
                                    duration-700
                                "
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition-opacity duration-700"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                style="display: none;"
                            >

                        @endif

                    @endforeach


                    {{-- Overlay --}}

                    <div class="absolute inset-0 bg-slate-900/68"></div>

                </div>


            @elseif ($isVisionUpdate && $visionUpdates->isNotEmpty())

                {{-- -------------------------------------------------
                     VISION UPDATE IMAGES
                     ------------------------------------------------- --}}

                <div class="pointer-events-none absolute inset-0">

                    @foreach ($visionUpdates as $index => $update)

                        @php
                            $path = trim((string) ($update->image_url ?? ''));

                            if ($path === '') {
                                $src = '';
                            } elseif (
                                preg_match('#^https?://#', $path) ||
                                str_starts_with($path, '/')
                            ) {
                                $src = $path;
                            } elseif (str_starts_with($path, 'storage/')) {
                                $src = asset($path);
                            } else {
                                $src = asset('storage/' . ltrim($path, '/'));
                            }
                        @endphp


                        @if ($src !== '')

                            <img
                                src="{{ $src }}"
                                alt="{{ $update->title ?? 'Vision Update' }}"
                                class="
                                    absolute
                                    inset-0
                                    h-full
                                    w-full
                                    object-cover
                                    transition-opacity
                                    duration-700
                                "
                                x-show="active === {{ $index }}"
                                x-transition:enter="transition-opacity duration-700"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                style="display: none;"
                            >

                        @endif

                    @endforeach


                    {{-- Overlay --}}

                    <div class="absolute inset-0 bg-white/75"></div>

                </div>

            @endif


            {{-- =====================================================
                 CONTENT
                 ===================================================== --}}

            <div class="relative z-10 flex h-full flex-col justify-between">


                {{-- =================================================
                     HEADING
                     ================================================= --}}

                <h2 class="text-4xl font-extrabold uppercase leading-tight sm:text-5xl">
                    {!! nl2br(e($column['heading'])) !!}
                </h2>


                {{-- =================================================
                     PARAGRAPH + CTA
                     ================================================= --}}

                <div class="max-w-md space-y-8">

                    <p
                        class="
                            text-[15px]
                            font-medium
                            leading-relaxed

                            {{ $isDark
                                ? 'text-white/85'
                                : 'text-slate-600'
                            }}
                        "
                    >
                        {{ $column['paragraph'] }}
                    </p>


                    <a
                        href="{{ Route::has($column['action']['route']) ? route($column['action']['route']) : '#' }}"
                        class="
                            inline-flex
                            w-full
                            items-center
                            justify-center
                            rounded-full
                            border-2
                            px-10
                            py-4
                            text-sm
                            font-semibold
                            transition-all
                            duration-200
                            sm:w-auto

                            {{ $isDark
                                ? 'border-white text-white hover:bg-white hover:text-slate-900'
                                : 'border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white'
                            }}
                        "
                    >
                        {{ $column['action']['label'] }}
                    </a>

                </div>

            </div>

        </div>

    @endforeach

</section>
```
