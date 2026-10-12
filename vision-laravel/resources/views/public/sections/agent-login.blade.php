@php
    /*
    |----------------------------------------------------------------------
    | Halaman Login Agent
    |----------------------------------------------------------------------
    | Halaman standalone (punya <html>, <head>, dan @vite sendiri).
    |
    | Variabel opsional (bisa dikirim dari route/controller):
    |   $loginAction  → URL tujuan form. Default: route('agent.login') jika ada.
    |   $bannerImage  → gambar banner di atas form.
    |   $showHeader   → false untuk menyembunyikan header navigasi.
    */

    $loginAction ??= \Illuminate\Support\Facades\Route::has('agent.login')
        ? route('agent.login')
        : '#';

    $bannerImage ??= asset('storage/image_products/agent-login.jpg');

    $showHeader ??= true;

    // Tujuan link menu di header (kembali ke beranda + #section)
    $homeUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');

    $inputClass = 'h-11 w-full rounded-lg border border-slate-200 bg-white pl-14 pr-4 text-sm text-slate-800 shadow-sm placeholder:text-slate-500 transition-colors focus:border-[#0A66E8] focus:outline-none focus:ring-2 focus:ring-[#0A66E8]/20';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Agent — {{ config('app.name') }}</title>

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


    <main class="relative min-h-screen overflow-hidden bg-white">

        {{-- ========================================================= --}}
        {{-- BANNER --}}
        {{-- ========================================================= --}}
        <div class="relative h-[24rem] w-full overflow-hidden bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#E3F2FB] sm:h-[27rem] lg:h-[29rem]">

            @if (!empty($bannerImage))
                <img
                    src="{{ $bannerImage }}"
                    alt="Vision Agent"
                    class="absolute inset-0 h-full w-full object-cover object-center"
                    onerror="this.style.visibility='hidden'"
                />
            @endif

            {{-- Pudar putih di atas (area header) --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 h-44 bg-linear-to-b from-white via-white/70 to-transparent" aria-hidden="true"></div>

            {{-- Overlay gelap halus agar judul putih terbaca --}}
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-slate-900/30 to-transparent" aria-hidden="true"></div>

            {{-- Pola hexagon putih (kiri) --}}
            <svg
                class="pointer-events-none absolute -left-4 top-16 hidden h-96 w-60 text-white/80 md:block"
                aria-hidden="true"
                style="-webkit-mask-image: linear-gradient(to right, black 30%, transparent 100%); mask-image: linear-gradient(to right, black 30%, transparent 100%);"
            >
                <defs>
                    <pattern id="login-hex-tl" width="56" height="98" patternUnits="userSpaceOnUse">
                        <path
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                            transform="scale(2)"
                        />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#login-hex-tl)" />
            </svg>

            {{-- Pola hexagon teal (kanan) --}}
            <svg
                class="pointer-events-none absolute -right-4 top-16 hidden h-96 w-60 text-teal-300/80 md:block"
                aria-hidden="true"
                style="-webkit-mask-image: linear-gradient(to left, black 30%, transparent 100%); mask-image: linear-gradient(to left, black 30%, transparent 100%);"
            >
                <defs>
                    <pattern id="login-hex-tr" width="56" height="98" patternUnits="userSpaceOnUse">
                        <path
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                            transform="scale(2)"
                        />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#login-hex-tr)" />
            </svg>

            {{-- Judul --}}
            <div class="absolute inset-x-0 bottom-6 z-10 px-6 text-center sm:bottom-8">
                <h1 class="text-2xl font-extrabold uppercase leading-tight tracking-tight text-white drop-shadow-[0_3px_10px_rgba(0,0,0,0.35)] sm:text-3xl lg:text-4xl">
                    Welcome to Vision Agent Platform
                </h1>
            </div>
        </div>


        {{-- Pola hexagon abu-abu (kiri bawah) --}}
        <svg
            class="pointer-events-none absolute bottom-0 left-0 hidden h-80 w-64 text-slate-200/80 md:block"
            aria-hidden="true"
            style="-webkit-mask-image: linear-gradient(to right, black 25%, transparent 100%); mask-image: linear-gradient(to right, black 25%, transparent 100%);"
        >
            <defs>
                <pattern id="login-hex-bl" width="56" height="98" patternUnits="userSpaceOnUse">
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                        transform="scale(2)"
                    />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#login-hex-bl)" />
        </svg>

        {{-- Pola hexagon biru muda (kanan bawah) --}}
        <svg
            class="pointer-events-none absolute bottom-0 right-0 hidden h-80 w-[30rem] text-sky-200/80 md:block"
            aria-hidden="true"
            style="-webkit-mask-image: linear-gradient(to left, black 25%, transparent 100%); mask-image: linear-gradient(to left, black 25%, transparent 100%);"
        >
            <defs>
                <pattern id="login-hex-br" width="56" height="98" patternUnits="userSpaceOnUse">
                    <path
                        fill="currentColor"
                        fill-rule="evenodd"
                        d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                        transform="scale(2)"
                    />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#login-hex-br)" />
        </svg>


        {{-- ========================================================= --}}
        {{-- FORM LOGIN --}}
        {{-- ========================================================= --}}
        <div class="relative z-10 mx-auto w-full max-w-[19rem] px-6 pb-20 pt-6 sm:max-w-xs">

            {{-- Pesan error (kredensial salah / validasi) --}}
            @if ($errors->any() || session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-700" role="alert">
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ $loginAction }}" method="POST" class="space-y-3.5">
                @csrf

                {{-- Username --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-2 left-0 flex w-12 items-center justify-center border-r border-slate-200 text-slate-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1" />
                        </svg>
                    </span>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        placeholder="Username"
                        autocomplete="username"
                        required
                        class="{{ $inputClass }}"
                    >
                </div>

                {{-- Password --}}
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-2 left-0 flex w-12 items-center justify-center border-r border-slate-200 text-slate-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="5" y="11" width="14" height="10" rx="2" />
                            <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                            <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none" />
                        </svg>
                    </span>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                        class="{{ $inputClass }}"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="h-10 w-full rounded-lg bg-linear-to-b from-[#1A74F0] to-[#0A5FE0] text-sm font-semibold text-white shadow-[0_10px_20px_-10px_rgba(10,95,224,0.8)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_24px_-10px_rgba(10,95,224,0.9)] active:translate-y-0"
                >
                    Login
                </button>
            </form>

        </div>

    </main>

</body>
</html>