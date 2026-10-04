@php
    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    | Diambil dari config/products.php (sumber yang sama dengan slider di
    | products.blade.php), sehingga judul & gambar selalu identik.
    |
    | $standalone = true bila file ini dibuka sebagai halaman sendiri
    | (route /produk), sehingga perlu <html>, <head>, dan CSS.
    */

    $standalone ??= false;

    $products ??= collect(config('products', []));

    // Tujuan tombol kembali
    $backUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');
@endphp


@if ($standalone)
<!DOCTYPE html>
<html lang="id" style="scroll-behavior: smooth;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Produk Layanan — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di file induk --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 antialiased">

    {{-- Kalau file induk punya header/navbar, include di sini, misalnya: --}}
    {{-- @include('public.header') --}}
@endif


{{-- Full page: setinggi layar, tanpa batas lebar --}}
<section
    id="detail-products"
    class="relative min-h-screen w-full overflow-hidden bg-slate-50 py-12 lg:py-16"
>

    {{-- Decorative mesh-gradient orbs, konsisten dengan section lain --}}
    <div
        class="pointer-events-none absolute inset-0 overflow-hidden"
        aria-hidden="true"
    >
        <div
            class="absolute -left-24 top-1/4 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"
        ></div>

        <div
            class="absolute -right-20 -bottom-24 h-112 w-112 rounded-full bg-[#F2A93B]/15 blur-3xl"
        ></div>
    </div>


    <div
        class="relative w-full px-6 sm:px-10 lg:px-16 xl:px-24 2xl:px-32"
    >

        {{-- Tombol kembali --}}
        <a
            href="{{ $backUrl }}"
            class="group mb-6 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:-translate-x-0.5 hover:border-[#F2A93B]/40 hover:text-[#F2A93B] hover:shadow-md"
        >
            <svg
                class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="m15 18-6-6 6-6" />
            </svg>

            Kembali
        </a>


        {{-- Heading --}}
        <h1
            class="text-4xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl"
        >
            Produk Layanan
        </h1>

        <div
            class="mt-3 h-1.5 w-20 rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C]"
        ></div>


        @if ($products->isEmpty())

            {{-- Empty state --}}
            <div
                class="mt-12 rounded-4xl border-2 border-dashed border-slate-200 bg-white/70 py-20 text-center backdrop-blur"
            >
                <svg
                    class="mx-auto mb-4 h-10 w-10 text-slate-300"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect x="3" y="4" width="18" height="14" rx="2" />
                    <circle cx="8.5" cy="9.5" r="1.5" />
                    <path d="m21 16-5-5-9 9" />
                </svg>

                <p class="text-sm font-medium text-slate-500">
                    Belum ada produk layanan.
                </p>
            </div>

        @else

            {{-- List: mengikuti scroll utama browser --}}
            <div class="mt-10 space-y-14 lg:space-y-20">

                @foreach ($products as $product)

                    <article
                        id="{{ $product['slug'] }}"
                        class="grid scroll-mt-8 items-start gap-6 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-12"
                    >

                        {{-- Left — image + tombol katalog --}}
                        <div class="flex flex-col items-center gap-8">

                            <div
                                class="relative aspect-4/3 w-full overflow-hidden rounded-[2.5rem] border border-white/60 bg-linear-to-br from-slate-100 via-white to-slate-200 shadow-[0_30px_80px_-30px_rgba(30,41,59,0.2)]"
                            >
                                {{-- Placeholder di belakang, tampil kalau gambar gagal dimuat --}}
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg
                                        class="h-10 w-10 text-slate-400"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <rect x="3" y="4" width="18" height="14" rx="2" />
                                        <circle cx="8.5" cy="9.5" r="1.5" />
                                        <path d="m21 16-5-5-9 9" />
                                    </svg>
                                </div>

                                <img
                                    src="{{ asset($product['image']) }}"
                                    alt="{{ $product['name'] }}"
                                    loading="lazy"
                                    class="relative h-full w-full object-cover"
                                    onerror="this.style.visibility='hidden'"
                                />
                            </div>

                            @if (!empty($product['catalog']))
                                <a
                                    href="{{ $product['catalog'] }}"
                                    class="inline-flex items-center justify-center rounded-full border border-slate-900 bg-white px-10 py-3 text-sm font-semibold text-slate-900 transition-all duration-200 hover:border-[#F2A93B] hover:bg-[#F2A93B] hover:text-white active:scale-95"
                                >
                                    Download Katalog
                                </a>
                            @endif

                        </div>


                        {{-- Right — text --}}
                        <div>

                            <h2
                                class="text-3xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 sm:text-4xl lg:text-5xl"
                            >
                                {{ $product['name'] }}
                            </h2>

                            <div
                                class="mt-6 space-y-5 text-base font-medium leading-relaxed text-slate-700 lg:text-lg"
                            >
                                @foreach ($product['details'] ?? [] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>

</section>


@if ($standalone)
    {{-- Kalau file induk punya footer: @include('public.footer') --}}
</body>
</html>
@endif