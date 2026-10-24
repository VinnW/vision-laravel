@php
    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    | $product dikirim dari ProductsController@showDetail (route /produk/{id?}):
    | HANYA 1 produk, yaitu yang diklik lewat tombol "Pelajari Lebih".
    | Kolom: image_url_left, image_url_top, text_title, text_desc,
    |        catalog_url, catalog_name.
    |
    | $standalone = true bila file ini dibuka sebagai halaman sendiri
    | (route /produk), sehingga perlu <html>, <head>, dan CSS.
    */

    $standalone ??= false;

    $product ??= \Illuminate\Support\Facades\DB::table('products')->orderBy('id')->first();

    // Nomor WhatsApp tujuan: format internasional, tanpa "+" dan tanpa 0 di depan
    // (contoh: 6281234567890). GANTI dengan nomor yang sebenarnya.
    $waNumber ??= '6281234567890';

    // Tujuan tombol kembali
    $backUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');

    // Path gambar dari database → URL (sama seperti helper di sub page lain)
    $imageSrc = function (?string $path): string {
        $p = trim((string) $path);

        if ($p === '') {
            return '';
        }

        if (preg_match('#^https?://#', $p) || str_starts_with($p, '/')) {
            return $p;
        }

        if (
            str_starts_with($p, 'storage/') ||
            str_starts_with($p, 'images/') ||
            str_starts_with($p, 'uploads/')
        ) {
            return url($p);
        }

        return url('storage/' . $p);
    };

    // Pecah deskripsi menjadi paragraf berdasarkan baris kosong
    $toParagraphs = function (?string $text): array {
        $text = trim((string) $text);

        if ($text === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', preg_split('/\R{2,}/', $text))
        ));
    };

    $title      = trim((string) ($product->text_title ?? ''));
    $imageLeft  = $imageSrc($product->image_url_left ?? '');
    $imageTop   = $imageSrc($product->image_url_top ?? '');
    $paragraphs = $toParagraphs($product->text_desc ?? '');
    $hasCatalog = !empty($product->catalog_url ?? null);

    // Unduh lewat route (selalu file katalog terbaru yang diunggah admin)
    $catalogUrl = ($product && $hasCatalog && \Illuminate\Support\Facades\Route::has('products.catalog'))
        ? route('products.catalog', ['id' => $product->id])
        : null;

    // Pesan otomatis yang terisi saat WhatsApp terbuka
    $waText = 'Halo, saya ingin bertanya tentang' . ($title !== '' ? ' ' . $title : ' produk layanan') . '.';
    $waUrl  = 'https://wa.me/' . preg_replace('/\D+/', '', $waNumber) . '?text=' . rawurlencode($waText);
@endphp


@if ($standalone)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title !== '' ? $title : 'Produk Layanan' }} — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di file induk --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white antialiased">
@endif


{{-- Full page: 2 kolom, foto full-bleed di kiri dengan judul overlay --}}
<section id="detail-products" class="grid min-h-screen w-full grid-cols-1 bg-white lg:grid-cols-[47fr_53fr]">

    @if ($product)

        {{-- ========================================================= --}}
        {{-- KIRI — banner besar + judul overlay --}}
        {{-- ========================================================= --}}
        <div class="relative min-h-[28rem] overflow-hidden bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#E3F2FB] lg:min-h-screen">

            @if ($imageLeft !== '')
                <img
                    src="{{ $imageLeft }}"
                    alt="{{ $title }}"
                    class="absolute inset-0 h-full w-full object-cover"
                    onerror="this.style.visibility='hidden'"
                />
            @endif

            {{-- Pudar putih di bawah (di belakang judul) --}}
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-3/5 bg-linear-to-t from-white/85 via-white/40 to-transparent" aria-hidden="true"></div>

            {{-- Tombol kembali --}}
            <a
                href="{{ $backUrl }}"
                class="group absolute left-5 top-5 z-20 inline-flex items-center gap-2 rounded-full border-2 border-[#10127A] bg-white/85 px-5 py-1.5 text-sm font-semibold text-[#10127A] backdrop-blur transition-all duration-200 hover:bg-[#10127A] hover:text-white lg:left-8 lg:top-8"
            >
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali
            </a>

            {{-- Judul overlay --}}
            <div class="absolute inset-x-0 bottom-0 z-10 px-6 pb-10 sm:px-10 lg:pb-14">
                <h1 class="text-4xl font-extrabold uppercase leading-[1.08] tracking-tight text-[#1F4E9E] sm:text-5xl lg:text-[3.4rem]">
                    {{ $title }}
                </h1>
            </div>
        </div>


        {{-- ========================================================= --}}
        {{-- KANAN — banner atas, deskripsi, tombol --}}
        {{-- ========================================================= --}}
        <div class="relative flex flex-col justify-center overflow-hidden bg-white px-6 py-12 sm:px-10 lg:px-14 lg:py-16">

            {{-- Pola hexagon biru muda (kanan bawah) --}}
            <svg
                class="pointer-events-none absolute -right-10 bottom-0 hidden h-72 w-96 text-sky-100 md:block"
                aria-hidden="true"
                style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 100%); mask-image: linear-gradient(to left, black 20%, transparent 100%);"
            >
                <defs>
                    <pattern id="detail-hex-br" width="56" height="98" patternUnits="userSpaceOnUse">
                        <path
                            fill="currentColor"
                            fill-rule="evenodd"
                            d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                            transform="scale(2)"
                        />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#detail-hex-br)" />
            </svg>

            <div class="relative max-w-2xl">

                {{-- Banner atas --}}
                @if ($imageTop !== '')
                    <img
                        src="{{ $imageTop }}"
                        alt="{{ $title }}"
                        class="h-auto w-full rounded-sm"
                        onerror="this.style.display='none'"
                    />
                @endif

                {{-- Deskripsi --}}
                <div class="mt-8 space-y-4 text-[12px] font-medium leading-snug text-[#1B2F8F] sm:text-[13px]">
                    @foreach ($paragraphs as $paragraph)
                        <p class="whitespace-pre-line">{{ $paragraph }}</p>
                    @endforeach
                </div>

                {{-- Tombol: Download PDF (katalog) & Hubungi Kami (WhatsApp) --}}
                <div class="mt-10 flex flex-wrap items-center gap-4">

                    @if ($catalogUrl)
                        <a
                            href="{{ $catalogUrl }}"
                            class="inline-flex min-w-[9.5rem] items-center justify-center rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,52,120,0.85)]"
                        >
                            Download PDF
                        </a>
                    @endif

                    <a
                        href="{{ $waUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-w-[9.5rem] items-center justify-center rounded-full bg-linear-to-r from-[#2E9B4E] to-[#1B7A3A] px-8 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,122,58,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,122,58,0.85)]"
                    >
                        Hubungi Kami
                    </a>

                </div>

            </div>
        </div>

    @else

        {{-- Empty state: belum ada produk --}}
        <div class="col-span-full flex min-h-screen flex-col items-center justify-center px-6 text-center">
            <p class="text-sm font-medium text-[#1B2F8F]">Belum ada produk layanan.</p>

            <a
                href="{{ $backUrl }}"
                class="mt-6 inline-flex items-center gap-2 rounded-full border-2 border-[#10127A] px-5 py-1.5 text-sm font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white"
            >
                Kembali
            </a>
        </div>

    @endif

</section>


@if ($standalone)
    {{-- Kalau file induk punya footer: @include('public.footer') --}}
</body>
</html>
@endif