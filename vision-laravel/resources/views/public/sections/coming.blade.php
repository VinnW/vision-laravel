@php
    /*
    |----------------------------------------------------------------------
    | Data
    |----------------------------------------------------------------------
    | $coming dikirim dari route (1 baris: image_url, title, description).
    | Jika file ini di-@include tanpa data, ambil sendiri dari tabel.
    |
    | $standalone = true bila file ini dibuka sebagai halaman sendiri
    | (route /event/coming), sehingga perlu <html>, <head>, dan CSS.
    */
    $standalone ??= false;

    $coming ??= \Illuminate\Support\Facades\DB::table('coming_up_next_section')->first();

    // Nomor WhatsApp tujuan pendaftaran: format internasional, tanpa "+" dan tanpa 0 di depan
    // (contoh: 6281234567890). GANTI dengan nomor yang sebenarnya.
    $waNumber ??= '6281234567890';

    // Tujuan tombol kembali
    $backUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');

    // Sama seperti src() di coming-panel.blade.php
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

    $title      = trim((string) ($coming->title ?? ''));
    $image      = $imageSrc($coming->image_url ?? '');
    $paragraphs = $toParagraphs($coming->description ?? '');

    // Pesan otomatis yang terisi saat WhatsApp terbuka
    $waText = 'Halo, saya ingin mendaftar' . ($title !== '' ? ' untuk ' . $title : '') . '.';
    $waUrl  = 'https://wa.me/' . preg_replace('/\D+/', '', $waNumber) . '?text=' . rawurlencode($waText);
@endphp

@if ($standalone)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title !== '' ? $title : 'Coming Up Next' }} — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di update.blade.php --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white antialiased">
@endif

{{-- Full page: poster di kiri, konten di kanan, latar putih dengan pola hexagon --}}
<section id="coming-up-next" class="relative flex min-h-screen w-full items-center overflow-hidden bg-white py-12 lg:py-16">

    {{-- Pola hexagon (kiri, abu-abu tipis) --}}
    <svg
        class="pointer-events-none absolute inset-y-0 left-0 hidden h-full w-72 text-slate-200/80 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to right, black 15%, transparent 100%); mask-image: linear-gradient(to right, black 15%, transparent 100%);"
    >
        <defs>
            <pattern id="coming-hex-l" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#coming-hex-l)" />
    </svg>

    {{-- Pola hexagon (kanan, biru muda) --}}
    <svg
        class="pointer-events-none absolute inset-y-0 right-0 hidden h-full w-96 text-sky-200/70 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 100%); mask-image: linear-gradient(to left, black 20%, transparent 100%);"
    >
        <defs>
            <pattern id="coming-hex-r" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#coming-hex-r)" />
    </svg>


    <div class="relative mx-auto grid w-full max-w-6xl items-center gap-10 px-6 lg:grid-cols-[minmax(0,19rem)_1fr] lg:gap-16 lg:px-10">

        {{-- Left — poster --}}
        <div class="mx-auto w-full max-w-xs lg:max-w-none">

            @if ($image !== '')
                <img
                    src="{{ $image }}"
                    alt="{{ $title !== '' ? $title : 'Coming Up Next' }}"
                    class="h-auto w-full rounded-sm shadow-[0_20px_40px_-18px_rgba(30,41,59,0.45)]"
                    onerror="this.style.visibility='hidden'"
                />
            @else
                <div class="flex aspect-2/3 w-full flex-col items-center justify-center rounded-sm bg-linear-to-br from-sky-50 via-white to-sky-100 text-center shadow-[0_20px_40px_-18px_rgba(30,41,59,0.3)]">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur">
                        <svg class="h-7 w-7 text-[#2F57A8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="14" rx="2" />
                            <circle cx="8.5" cy="9.5" r="1.5" />
                            <path d="m21 16-5-5-9 9" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold uppercase tracking-wide text-[#2F57A8]">Gambar Event</p>
                </div>
            @endif

        </div>

        {{-- Right — content --}}
        <div>

            {{-- Tombol kembali --}}
            <a
                href="{{ $backUrl }}"
                class="group mb-8 inline-flex items-center gap-2 rounded-full border-2 border-[#10127A] bg-white/80 px-5 py-1.5 text-sm font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white"
            >
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali
            </a>

            @if ($coming)

                {{-- Judul halaman --}}
                <p class="text-4xl font-extrabold uppercase leading-[1.1] tracking-tight text-[#1F4E9E] sm:text-5xl">
                    Event Akan Datang
                </p>

                {{-- Title event (dari database) --}}
                <h1 class="mt-10 bg-linear-to-r from-slate-500 via-slate-600 to-slate-500 bg-clip-text text-2xl font-extrabold uppercase leading-[1.1] tracking-tight text-transparent sm:text-3xl lg:text-[2.1rem]">
                    {{ $title }}
                </h1>

                {{-- Description --}}
                <div class="mt-8 max-w-xl space-y-5 text-[13px] font-medium leading-snug text-slate-700 sm:text-sm">
                    @foreach ($paragraphs as $paragraph)
                        <p class="whitespace-pre-line">{{ $paragraph }}</p>
                    @endforeach
                </div>

                {{-- CTA: tetap membuka WhatsApp --}}
                <a
                    href="{{ $waUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-full bg-linear-to-r from-[#2E9B4E] to-[#1B7A3A] px-8 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,122,58,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,122,58,0.85)] sm:w-auto sm:min-w-[9rem]"
                >
                    Hubungi Kami
                </a>

            @else

                {{-- Empty state --}}
                <div class="rounded-[2rem] border-2 border-dashed border-[#6BB0FF]/60 bg-white/70 py-20 text-center backdrop-blur">
                    <p class="text-sm font-medium text-[#1B2F8F]">Belum ada event mendatang.</p>
                </div>

            @endif

        </div>

    </div>

</section>

@if ($standalone)
</body>
</html>
@endif