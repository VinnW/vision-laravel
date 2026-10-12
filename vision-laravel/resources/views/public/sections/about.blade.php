{{--
    About Section
    - Foto keluarga di kiri, memudar ke putih di sisi kanan
    - Judul + artikel (rata kiri-kanan) di sisi kanan, warna biru tua
    - Ikon sosial media bulat biru tua di kanan bawah
    - Data ($image, $title, $paragraphs, $socials) tetap bisa di-override dari luar
--}}

@php
    $image ??= asset('storage/image_products/company-profile.jpg');

    $title ??= 'Vision Ada Untuk Anda';

    $paragraphs ??= [
        'VISION adalah sebuah agency asuransi yang hadir dengan satu tujuan: membantu masyarakat memiliki perlindungan yang tepat untuk menghadapi berbagai kemungkinan dalam hidup.',
        'Kami percaya bahwa asuransi bukan sekadar produk finansial, tetapi merupakan bentuk kepedulian dan persiapan untuk orang-orang yang kita cintai.',
        'Dengan semangat “Satu Visi, Satu Hati”, VISION membangun hubungan jangka panjang dengan nasabah melalui pelayanan yang profesional, personal, transparan, dan berorientasi pada kebutuhan setiap individu.',
        'Kami hadir untuk membantu Anda memahami pilihan perlindungan, merencanakan masa depan, serta memberikan solusi yang sesuai dengan kondisi dan tujuan finansial Anda.',
        'Karena masa depan tidak selalu bisa diprediksi, tetapi kita bisa mempersiapkannya.',
    ];

    $socials ??= [
        ['label' => 'Instagram', 'url' => '#'],
        ['label' => 'Facebook', 'url' => '#'],
        ['label' => 'Twitter', 'url' => '#'],
        ['label' => 'WhatsApp', 'url' => '#'],
    ];
@endphp

<section id="about" class="relative overflow-hidden bg-white">

    {{-- ========================================================= --}}
    {{-- IMAGE PANEL (kiri) --}}
    {{-- ========================================================= --}}
    <div class="relative h-80 w-full sm:h-[26rem] lg:absolute lg:inset-y-0 lg:left-0 lg:h-auto lg:w-[62%]">

        @if (!empty($image))
            <img
                src="{{ $image }}"
                alt="{{ config('app.name', 'Perusahaan') }} — company profile"
                class="absolute inset-0 h-full w-full object-cover object-[30%_top]"
            />
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#FFF3E2]">
                <svg class="h-16 w-16 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="14" rx="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="m21 15-5-5-9 9" />
                </svg>
            </div>
        @endif

        {{-- Pudar ke putih: bawah (mobile) --}}
        <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-white via-transparent via-40% to-transparent lg:hidden" aria-hidden="true"></div>

        {{-- Pudar ke putih: kanan (desktop) --}}
        <div class="pointer-events-none absolute inset-0 hidden bg-linear-to-r from-transparent from-55% to-white lg:block" aria-hidden="true"></div>

        {{-- Pudar ke putih: bawah (desktop, halus) --}}
        <div class="pointer-events-none absolute inset-x-0 bottom-0 hidden h-28 bg-linear-to-t from-white/80 to-transparent lg:block" aria-hidden="true"></div>

        {{-- Pola hexagon garis putih (kiri bawah) --}}
        <svg
            class="pointer-events-none absolute -bottom-4 left-0 hidden h-72 w-80 text-white/90 md:block"
            aria-hidden="true"
            style="-webkit-mask-image: linear-gradient(to right, black 30%, transparent 100%); mask-image: linear-gradient(to right, black 30%, transparent 100%);"
        >
            <defs>
                <pattern id="about-hex-bl" width="56" height="98" patternUnits="userSpaceOnUse">
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                        transform="scale(2)"
                    />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#about-hex-bl)" />
        </svg>
    </div>

    {{-- Pola hexagon biru muda (kanan atas) --}}
    <svg
        class="pointer-events-none absolute -right-10 top-0 hidden h-80 w-[34rem] text-sky-200/50 lg:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 95%); mask-image: linear-gradient(to left, black 20%, transparent 95%);"
    >
        <defs>
            <pattern id="about-hex-tr" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#about-hex-tr)" />
    </svg>


    {{-- ========================================================= --}}
    {{-- ARTICLE (kanan) --}}
    {{-- ========================================================= --}}
    <div class="relative z-10 mx-auto max-w-7xl px-6 pb-16 lg:px-10 lg:pb-0">
        <div class="grid lg:min-h-[640px] lg:grid-cols-2">

            <div class="flex flex-col justify-between py-4 lg:col-start-2 lg:py-16 lg:pl-6">

                <div>
                    <h1 class="text-4xl font-extrabold uppercase leading-[1.1] tracking-tight text-[#1F4E9E] sm:text-5xl">
                        {{ $title }}
                    </h1>

                    <div class="mt-8 space-y-5 text-[13px] font-medium leading-snug text-[#1B2F8F] sm:text-sm lg:mt-10 lg:text-justify">
                        @foreach ($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

                {{-- Social links --}}
                <div class="mt-10 flex items-center gap-3 lg:justify-end">
                    @foreach ($socials as $social)
                        <a
                            href="{{ $social['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['label'] }}"
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#10127A] text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#1F4FD1] hover:shadow-md"
                        >
                            @switch($social['label'])
                                @case('Instagram')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" /></svg>
                                    @break
                                @case('Facebook')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.6h2.55l.38-2.96h-2.93V8.55c0-.86.24-1.44 1.47-1.44h1.57V4.46A21 21 0 0 0 14.3 4.3c-2.24 0-3.78 1.37-3.78 3.87v2.27H8v2.96h2.52V21h3Z" /></svg>
                                    @break
                                @case('Twitter')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.7.3-1.5.5-2.4.7.9-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1-.8-.8-1.8-1.3-3-1.3-2.3 0-4.1 1.8-4.1 4.1 0 .3 0 .6.1.9C8.4 8.8 5.4 7.2 3.4 4.7c-.4.6-.6 1.3-.6 2.1 0 1.4.7 2.7 1.8 3.4-.7 0-1.3-.2-1.9-.5v.1c0 2 1.4 3.6 3.3 4-.3.1-.7.1-1.1.1-.3 0-.5 0-.8-.1.5 1.6 2 2.8 3.8 2.8-1.4 1.1-3.2 1.8-5.1 1.8-.3 0-.7 0-1-.1 1.8 1.2 4 1.8 6.3 1.8 7.5 0 11.7-6.3 11.7-11.7v-.5c.8-.6 1.5-1.3 2.1-2.1z" /></svg>
                                    @break
                                @case('WhatsApp')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.7.1-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.8-.8-1.4-1.7-1.6-2-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.3zM12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2z" /></svg>
                                    @break
                                @case('TikTok')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 5.82c-.7-.77-1.14-1.76-1.24-2.82H12.4v13.44c0 1.48-1.2 2.68-2.68 2.68a2.68 2.68 0 0 1 0-5.36c.28 0 .55.04.8.12V10.9a5.6 5.6 0 0 0-.8-.06 5.62 5.62 0 1 0 5.62 5.62V8.9a8.36 8.36 0 0 0 4.86 1.55V7.5a5.7 5.7 0 0 1-3.6-1.68Z" /></svg>
                                    @break
                                @case('YouTube')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 7.6a2.7 2.7 0 0 0-1.9-1.9C18 5.2 12 5.2 12 5.2s-6 0-7.7.5A2.7 2.7 0 0 0 2.4 7.6 28 28 0 0 0 2 12a28 28 0 0 0 .4 4.4 2.7 2.7 0 0 0 1.9 1.9c1.7.5 7.7.5 7.7.5s6 0 7.7-.5a2.7 2.7 0 0 0 1.9-1.9c.3-1.45.4-2.93.4-4.4a28 28 0 0 0-.4-4.4ZM10 15V9l5.2 3-5.2 3Z" /></svg>
                                    @break
                            @endswitch
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</section>