@php
    /*
    |----------------------------------------------------------------------
    | Halaman Join Agent
    |----------------------------------------------------------------------
    | Sub page (dibuka dari tombol "Join Agent" di hero).
    |
    | Variabel opsional (bisa dikirim dari route/controller):
    |   $standalone   → true (default) = halaman penuh dengan <html>, <head>, @vite.
    |                   false = dipakai lewat @include di halaman lain.
    |   $bannerImage  → foto latar (orang-orang berkaos biru).
    |   $formAction   → URL tujuan form. Default: route('join-agent.submit') jika ada.
    |   $showHeader   → true untuk menampilkan header navigasi (default: false,
    |                   karena ini sub page).
    */

    $standalone ??= true;

    $bannerImage ??= asset('storage/image_products/join-agent.jpg');

    $formAction ??= \Illuminate\Support\Facades\Route::has('join-agent.submit')
        ? route('join-agent.submit')
        : '#';

    $showHeader ??= false;

    // Tujuan tombol kembali (dan link menu header bila ditampilkan)
    $homeUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');

    // Kelas input & label dipakai berulang agar konsisten
    $labelClass = 'mb-1 block text-[11px] font-semibold text-slate-900';
    $inputClass = 'w-full rounded-none border border-slate-500 bg-white/90 px-3 py-1.5 text-sm font-medium text-slate-900 placeholder:text-xs placeholder:font-normal placeholder:text-slate-400 transition-colors focus:border-[#2F57A8] focus:outline-none focus:ring-2 focus:ring-[#2F57A8]/20';
@endphp


@if ($standalone)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Join Agent — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di file induk halaman utama --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white antialiased">

    @if ($showHeader)
        {{-- Header sama seperti halaman utama; menu diarahkan ke beranda + #section --}}
        {{-- Ubah daftar path di bawah jika lokasi header.blade.php Anda berbeda --}}
        @includeFirst(
            ['public.header', 'public.sections.header', 'public.partials.header', 'partials.header', 'layouts.header', 'components.header', 'header'],
            ['navBase' => $homeUrl]
        )
    @endif
@endif


<section id="join-agent" class="relative min-h-screen w-full overflow-hidden bg-white">

    {{-- ========================================================= --}}
    {{-- LATAR FOTO --}}
    {{-- ========================================================= --}}
    <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#E3F2FB]" aria-hidden="true">

        @if (!empty($bannerImage))
            <img
                src="{{ $bannerImage }}"
                alt=""
                class="h-full w-full object-cover object-[20%_bottom]"
                onerror="this.style.visibility='hidden'"
            >
        @endif

        {{-- Mobile: putih rata agar form terbaca --}}
        <div class="absolute inset-0 bg-white/75 lg:hidden"></div>

        {{-- Desktop: pudar putih dari tengah ke kanan --}}
        <div class="absolute inset-0 hidden bg-linear-to-r from-transparent from-40% via-white/80 via-62% to-white lg:block"></div>

        {{-- Pudar putih di atas --}}
        <div class="absolute inset-x-0 top-0 h-44 bg-linear-to-b from-white via-white/70 to-transparent"></div>
    </div>

    {{-- Pola hexagon putih (kiri) --}}
    <svg
        class="pointer-events-none absolute left-0 top-24 hidden h-full max-h-[34rem] w-60 text-white/90 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to right, black 25%, transparent 100%); mask-image: linear-gradient(to right, black 25%, transparent 100%);"
    >
        <defs>
            <pattern id="join-hex-l" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#join-hex-l)" />
    </svg>

    {{-- Pola hexagon biru muda (kanan) --}}
    <svg
        class="pointer-events-none absolute inset-y-0 right-0 hidden h-full w-[26rem] text-sky-200/80 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 25%, transparent 100%); mask-image: linear-gradient(to left, black 25%, transparent 100%);"
    >
        <defs>
            <pattern id="join-hex-r" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#join-hex-r)" />
    </svg>


    {{-- ========================================================= --}}
    {{-- KONTEN --}}
    {{-- ========================================================= --}}
    <div class="relative z-10 mx-auto max-w-5xl px-6 pb-16 {{ $showHeader ? 'pt-32 lg:pt-36' : 'pt-10 lg:pt-14' }} lg:px-10">

        <div class="grid gap-10 lg:grid-cols-[1.15fr_1fr] lg:gap-12">

            {{-- ================= KIRI: Judul ================= --}}
            <div>

                {{-- Tombol kembali --}}
                <a
                    href="{{ $homeUrl }}"
                    class="group mb-8 inline-flex items-center gap-2 rounded-full border-2 border-[#10127A] bg-white/80 px-5 py-1.5 text-sm font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white"
                >
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Kembali
                </a>

                <h1 class="text-4xl font-extrabold uppercase leading-[1.08] tracking-tight text-[#1F4E9E] sm:text-5xl lg:text-[3.4rem]">
                    Sukses Bersama<br>Kami
                </h1>

                <p class="mt-5 max-w-md text-[13px] font-medium leading-snug text-slate-900 sm:text-sm">
                    Bergabung bersama Vision, kembangkan potensi, raih peluang, dan tumbuh bersama menuju kesuksesan.
                </p>
            </div>


            {{-- ================= KANAN: Form ================= --}}
            <div class="w-full max-w-sm lg:mt-12 lg:max-w-none">

                <h2 class="text-3xl font-medium uppercase tracking-tight text-slate-900">
                    Join Agent
                </h2>

                {{-- Notifikasi sukses (aktif setelah form disambungkan ke controller) --}}
                @if (session('success'))
                    <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ $formAction }}"
                    method="POST"
                    class="mt-4 space-y-3.5"
                >
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="{{ $labelClass }}">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="{{ $inputClass }}"
                            placeholder="Masukkan nama lengkap"
                        >
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="{{ $labelClass }}">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            class="{{ $inputClass }}"
                            placeholder="nama@email.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label for="city" class="{{ $labelClass }}">Kota</label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            value="{{ old('city') }}"
                            class="{{ $inputClass }}"
                            placeholder="Kota domisili"
                        >
                        @error('city')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label for="phone" class="{{ $labelClass }}">No Telepon</label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            class="{{ $inputClass }}"
                            placeholder="08xxxxxxxxxx"
                        >
                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label for="message" class="{{ $labelClass }}">Pesan</label>
                        <textarea
                            name="message"
                            id="message"
                            rows="7"
                            class="{{ $inputClass }} resize-none"
                            placeholder="Ceritakan singkat latar belakang dan alasan Anda ingin bergabung"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="mt-2 inline-flex min-w-[7.5rem] items-center justify-center rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-10 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,52,120,0.85)]"
                    >
                        Daftar
                    </button>
                </form>

            </div>

        </div>
    </div>

</section>


@if ($standalone)
</body>
</html>
@endif