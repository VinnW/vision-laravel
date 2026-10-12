{{--
    Service Section
    - Blok atas: foto full-width memudar ke putih, judul + artikel di kanan
    - Blok bawah: 2 panel (Zurich & Smart Calculator) dengan gambar + pudar putih
    - Judul biru tua, tombol pill navy
    - Logika kalkulator (modal, nilai, durasi, estimasi) tidak diubah
--}}

@php
    $introTitle ??= 'Kami Selalu Ada Untuk Anda';
    $introSubtitle ??= 'Pendampingan yang Hadir di Setiap Langkah';

    $intro ??= [
        'Bagi kami, layanan asuransi tidak berhenti setelah polis diterbitkan. Kami hadir untuk memastikan setiap kebutuhan Anda mendapatkan perhatian dan tindak lanjut yang tepat.',
        'Mulai dari konsultasi, pengelolaan polis, follow-up secara berkala, hingga proses klaim, tim kami siap mendampingi Anda dengan respons yang cepat, komunikasi yang jelas, dan solusi yang tepat.',
        'Karena perlindungan terbaik bukan hanya tentang memiliki asuransi, tetapi tentang memiliki partner yang selalu siap membantu ketika Anda membutuhkannya.',
    ];

    $introImage ??= asset('storage/image_products/good-services.jpg');

    // Jika ada file logo di public/storage/image_products/zurich-logo.png, otomatis dipakai.
    $zurichLogoPath = 'storage/image_products/zurich-logo.png';
    $hasZurichLogo  = file_exists(public_path($zurichLogoPath));

    $services ??= [
        [
            'align'      => 'right',   // posisi konten: 'right' | 'left'
            'fade'       => 'top',     // pudar putih: 'top' | 'left'
            'brand'      => 'Zurich',
            'logo'       => $hasZurichLogo ? asset($zurichLogoPath) : null,
            'heading'    => 'Mitra Terpercaya',
            'image'      => asset('storage/image_products/zuric-picture.jpg'),
            'paragraphs' => [
                'Bersama Zurich, Vision hadir bukan hanya sebagai penyedia solusi asuransi, tetapi sebagai partner yang selalu siap mendampingi Anda di setiap langkah.',
            ],
        ],
        [
            'align'   => 'left',
            'fade'    => 'left',
            'heading' => "Hitung Premi\nLayanan Anda",
            'image'   => asset('storage/image_products/smart-calculator.jpg'),
            'action'  => [
                'label' => 'Smart Calculator',
                'type'  => 'calculator',
            ],
        ],
    ];
@endphp

<div x-data="{ calculatorOpen: false, nilai: 100000000, durasi: 10, get estimasi() { return this.durasi > 0 ? Math.round((this.nilai * 0.012 * this.durasi)) : 0 } }">

    {{-- ============ Top intro block ============ --}}
    <section id="service" class="relative scroll-mt-24 overflow-hidden bg-white">

        {{-- Foto latar --}}
        <img
            src="{{ $introImage }}"
            alt="{{ $introTitle }}"
            class="absolute inset-0 h-full w-full object-cover object-[25%_center]"
        />

        {{-- Overlay mobile: putih rata agar teks terbaca --}}
        <div class="pointer-events-none absolute inset-0 bg-white/80 lg:hidden" aria-hidden="true"></div>

        {{-- Overlay desktop: pudar ke putih dari tengah ke kanan --}}
        <div class="pointer-events-none absolute inset-0 hidden bg-linear-to-r from-transparent from-35% via-white/85 via-60% to-white lg:block" aria-hidden="true"></div>

        {{-- Pudar putih di atas (area header) --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-28 bg-linear-to-b from-white via-white/60 to-transparent" aria-hidden="true"></div>

        {{-- Pola hexagon garis putih (kiri) --}}
        <svg
            class="pointer-events-none absolute left-0 top-10 hidden h-80 w-80 text-white/90 md:block"
            aria-hidden="true"
            style="-webkit-mask-image: linear-gradient(to right, black 20%, transparent 100%); mask-image: linear-gradient(to right, black 20%, transparent 100%);"
        >
            <defs>
                <pattern id="service-hex-tl" width="56" height="98" patternUnits="userSpaceOnUse">
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                        transform="scale(2)"
                    />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#service-hex-tl)" />
        </svg>

        {{-- Konten --}}
        <div class="relative mx-auto max-w-7xl px-6 py-12 lg:px-10 lg:py-4">
    <div class="grid lg:min-h-[5rem] lg:grid-cols-2 lg:items-center">

                <div class="lg:col-start-2 lg:pl-4">
                    <h1 class="text-3xl font-extrabold uppercase leading-[1.1] tracking-tight text-[#1F4E9E] sm:text-4xl lg:text-[2.6rem]">
                        {{ $introTitle }}
                    </h1>

                    <p class="mt-6 text-[13px] font-bold leading-snug text-[#1B2F8F] sm:text-sm">
                        {{ $introSubtitle }}
                    </p>

                    <div class="mt-4 space-y-4 text-[13px] font-medium leading-snug text-[#1B2F8F] sm:text-sm">
                        @foreach ($intro as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ Bottom split service blocks ============ --}}
    <section class="grid grid-cols-1 md:grid-cols-2">
        @foreach ($services as $service)
            @php
                $alignRight = ($service['align'] ?? 'left') === 'right';
                $fade       = $service['fade'] ?? 'left';
            @endphp

            <div class="relative flex min-h-[22rem] flex-col justify-between overflow-hidden bg-slate-100 px-8 py-8 lg:min-h-[20rem] lg:px-12">

                {{-- Gambar --}}
                @if (!empty($service['image']))
                    <img
                        src="{{ $service['image'] }}"
                        alt="{{ $service['brand'] ?? strip_tags($service['heading']) }}"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="lazy"
                    />
                @endif

                {{-- Pudar putih --}}
                @if ($fade === 'top')
                    <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-white/70 via-white/30 to-transparent" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-0 bg-linear-to-l from-white/50 to-transparent" aria-hidden="true"></div>
                @else
                    <div class="pointer-events-none absolute inset-0 bg-white/60 md:hidden" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-0 hidden bg-linear-to-r from-white/95 via-white/55 via-45% to-transparent md:block" aria-hidden="true"></div>
                @endif

                {{-- Brand / logo (opsional) --}}
                @if (!empty($service['logo']) || !empty($service['brand']))
                    <div class="relative flex {{ $alignRight ? 'justify-end lg:pr-4' : 'justify-start' }}">
                        @if (!empty($service['logo']))
                            <img src="{{ $service['logo'] }}" alt="{{ $service['brand'] ?? '' }}" class="h-14 w-auto" />
                        @else
                            <div class="flex items-center gap-3 text-[#2167AE]">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#2167AE] text-xl font-extrabold text-white">Z</span>
                                <span class="font-serif text-3xl font-semibold uppercase tracking-wider">{{ $service['brand'] }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="relative"></div>
                @endif

                {{-- Heading + action --}}
                <div class="relative {{ $alignRight ? 'lg:ml-auto lg:w-[80%] lg:text-right' : '' }} {{ empty($service['paragraphs']) ? 'my-10' : 'mt-6 mb-26' }}">
    <h2 class="text-2xl font-extrabold uppercase leading-[1.1] tracking-tight text-[#1F4E9E] sm:text-3xl lg:text-[2rem]">
        {!! nl2br(e($service['heading'])) !!}
    </h2>

                    @if (!empty($service['action']))
                        @php
                            $btnClass = 'mt-4 inline-block rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-6 py-1.5 text-xs font-medium text-white shadow-[0_10px_22px_-10px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_26px_-10px_rgba(27,52,120,0.8)]';
                        @endphp

                        @if ($service['action']['type'] === 'calculator')
                            <button type="button" @click="calculatorOpen = true" class="{{ $btnClass }}">
                                {{ $service['action']['label'] }}
                            </button>
                        @else
                            <a href="{{ $service['action']['href'] ?? '#' }}" class="{{ $btnClass }}">
                                {{ $service['action']['label'] }}
                            </a>
                        @endif
                    @endif
                </div>

                {{-- Deskripsi --}}
                @if (!empty($service['paragraphs']))
                    <div class="relative space-y-3 text-[13px] font-medium leading-snug text-[#1B2F8F] sm:text-sm {{ $alignRight ? 'lg:ml-auto lg:w-[80%] lg:text-right' : '' }}">
    @foreach ($service['paragraphs'] as $paragraph)
    <p class="relative -top-24">{{ $paragraph }}</p>
@endforeach
</div>
                @else
                    <div class="relative"></div>
                @endif
            </div>
        @endforeach
    </section>

    {{-- ============ Calculator modal ============ --}}
    <div
        x-show="calculatorOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 p-6 backdrop-blur-sm"
        style="display: none;"
        @keydown.escape.window="calculatorOpen = false"
    >
        <div
            @click.outside="calculatorOpen = false"
            x-show="calculatorOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-8 shadow-2xl"
        >
            <button
                @click="calculatorOpen = false"
                type="button"
                aria-label="Tutup kalkulator"
                class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>

            <h3 class="text-xl font-extrabold text-[#1F4E9E]">Smart Calculator</h3>
            <p class="mt-1 text-sm font-medium text-[#1B2F8F]/70">Hitung estimasi perlindungan Anda.</p>

            <div class="mt-6 space-y-5">
                <label class="block">
                    <span class="text-sm font-semibold text-[#1B2F8F]">Nilai Pertanggungan (Rp)</span>
                    <input
                        type="number"
                        x-model.number="nilai"
                        min="0"
                        step="1000000"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none transition-colors focus:border-[#2F57A8]"
                    />
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-[#1B2F8F]">Durasi (tahun)</span>
                    <input
                        type="number"
                        x-model.number="durasi"
                        min="1"
                        max="30"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none transition-colors focus:border-[#2F57A8]"
                    />
                </label>

                {{-- NOTE: formula di atas (nilai * 0.012 * durasi) hanyalah dummy —
                     ganti sesuai perhitungan bisnis Anda yang sebenarnya. --}}
                <div class="rounded-2xl bg-sky-50 px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#1B2F8F]/60">Estimasi Premi</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#1F4E9E]" x-text="'Rp ' + estimasi.toLocaleString('id-ID')"></p>
                </div>
            </div>
        </div>
    </div>
</div>