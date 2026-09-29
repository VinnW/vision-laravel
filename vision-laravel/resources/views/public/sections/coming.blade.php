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
<body class="bg-slate-50 antialiased">
@endif

{{-- Full page: 2 kolom, gambar full-bleed di kiri --}}
<section id="coming-up-next" class="grid min-h-screen w-full grid-cols-1 bg-slate-50 lg:grid-cols-2">

    {{-- Left — image --}}
    <div class="relative min-h-[45vh] overflow-hidden bg-slate-200 lg:min-h-screen">

        @if ($image !== '')
            <img
                src="{{ $image }}"
                alt="{{ $title !== '' ? $title : 'Coming Up Next' }}"
                class="absolute inset-0 h-full w-full object-cover"
                onerror="this.style.visibility='hidden'"
            />
        @else
            <div class="flex h-full min-h-[45vh] w-full flex-col items-center justify-center text-center lg:min-h-screen">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur">
                    <svg class="h-7 w-7 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="14" rx="2" />
                        <circle cx="8.5" cy="9.5" r="1.5" />
                        <path d="m21 16-5-5-9 9" />
                    </svg>
                </div>
                <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Gambar Event</p>
            </div>
        @endif

    </div>

    {{-- Right — content --}}
    <div class="relative flex flex-col justify-center overflow-hidden bg-slate-50 px-6 py-14 sm:px-10 lg:px-16 lg:py-20 xl:px-24">

        {{-- Decorative mesh-gradient orbs, consistent with the about section --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -right-24 top-1/4 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
            <div class="absolute -left-20 -bottom-24 h-112 w-112 rounded-full bg-[#F2A93B]/15 blur-3xl"></div>
        </div>

        <div class="relative">

            {{-- Tombol kembali --}}
            <a
                href="{{ $backUrl }}"
                class="group mb-8 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-[#F2A93B]/40 hover:text-[#F2A93B] hover:shadow-md"
            >
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Kembali
            </a>

            @if ($coming)

                {{-- Title --}}
                <h1 class="text-5xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 sm:text-6xl lg:text-7xl">
                    {{ $title }}
                </h1>

                <div class="mt-3 h-1.5 w-20 rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C]"></div>

                {{-- Description --}}
                <div class="mt-8 max-w-2xl space-y-5 text-base font-medium leading-relaxed text-slate-700 lg:text-lg">
                    @foreach ($paragraphs as $paragraph)
                        <p class="whitespace-pre-line">{{ $paragraph }}</p>
                    @endforeach
                </div>

                {{-- WhatsApp + CTA --}}
                <div class="mt-12 flex max-w-2xl flex-wrap items-center justify-center gap-4">

                    <a
                        href="{{ $waUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="WhatsApp"
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#25D366] text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                    </a>

                    <a
                        href="{{ $waUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex w-full items-center justify-center rounded-full border-2 border-slate-900 px-10 py-4 text-sm font-semibold text-slate-900 transition-all duration-200 hover:bg-slate-900 hover:text-white sm:w-auto sm:min-w-64"
                    >
                        Daftar Sekarang
                    </a>

                </div>

            @else

                {{-- Empty state --}}
                <div class="rounded-4xl border-2 border-dashed border-slate-200 bg-white/70 py-20 text-center backdrop-blur">
                    <p class="text-sm font-medium text-slate-500">Belum ada event mendatang.</p>
                </div>

            @endif

        </div>

    </div>

</section>

@if ($standalone)
</body>
</html>
@endif