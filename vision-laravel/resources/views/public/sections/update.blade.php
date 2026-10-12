@php
    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    | $updates dikirim dari route. Jika file ini di-@include tanpa data,
    | ambil sendiri dari tabel yang diisi oleh vision-update-panel.
    |
    | $standalone = true bila file ini dibuka sebagai halaman sendiri
    | (route /event/update), sehingga perlu <html>, <head>, dan CSS.
    */

    $standalone ??= false;

    $updates ??= \Illuminate\Support\Facades\DB::table('vision_update_section')
        ->orderByDesc('id')
        ->get();

    // Tujuan tombol kembali
    $backUrl = \Illuminate\Support\Facades\Route::has('home')
        ? route('home')
        : url('/');

    // Sama seperti src() di vision-update-panel.blade.php
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
@endphp


@if ($standalone)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vision Update — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di file induk yang memuat about.blade.php --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white antialiased">
@endif


{{-- Full page: setinggi layar, tanpa batas lebar --}}
<section
    id="vision-update"
    class="relative min-h-screen w-full overflow-hidden bg-white py-12 lg:py-16"
>

    {{-- Pola hexagon (kiri, abu-abu tipis) --}}
    <svg
        class="pointer-events-none absolute inset-y-0 left-0 hidden h-full w-72 text-slate-200/80 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to right, black 15%, transparent 100%); mask-image: linear-gradient(to right, black 15%, transparent 100%);"
    >
        <defs>
            <pattern id="update-hex-l" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#update-hex-l)" />
    </svg>

    {{-- Pola hexagon (kanan, biru muda) --}}
    <svg
        class="pointer-events-none absolute inset-y-0 right-0 hidden h-full w-96 text-sky-200/70 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 100%); mask-image: linear-gradient(to left, black 20%, transparent 100%);"
    >
        <defs>
            <pattern id="update-hex-r" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#update-hex-r)" />
    </svg>


    <div class="relative mx-auto max-w-6xl px-6 lg:px-10">

        {{-- Judul halaman (tersembunyi secara visual, tetap ada untuk SEO & aksesibilitas) --}}
        <h2 class="sr-only">Vision Update</h2>

        {{-- Tombol kembali --}}
        <a
            href="{{ $backUrl }}"
            class="group mb-8 inline-flex items-center gap-2 rounded-full border-2 border-[#10127A] bg-white/80 px-5 py-1.5 text-sm font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white"
        >
            <svg
                class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="m15 18-6-6 6-6" />
            </svg>

            Kembali
        </a>


        @if ($updates->isEmpty())

            {{-- Empty state --}}
            <div
                class="mt-4 rounded-[2rem] border-2 border-dashed border-[#6BB0FF]/60 bg-white/70 py-20 text-center backdrop-blur"
            >
                <svg
                    class="mx-auto mb-4 h-10 w-10 text-[#6BB0FF]"
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

                <p class="text-sm font-medium text-[#1B2F8F]">
                    Belum ada Vision Update.
                </p>
            </div>

        @else

            {{-- List --}}
            {{-- Seluruh halaman mengikuti scroll utama browser. --}}
            <div data-vu-scroll class="space-y-12 lg:space-y-14">

                @foreach ($updates as $update)

                    @php
                        $src = $imageSrc($update->image_url ?? '');
                        $paragraphs = $toParagraphs($update->text_body ?? '');
                    @endphp


                    <article
                        data-vu-item
                        class="grid items-start gap-6 lg:grid-cols-[18rem_1fr] lg:gap-14"
                    >

                        {{-- Left — image --}}
                        <div
                            class="relative aspect-19/20 w-full max-w-sm overflow-hidden rounded-[2rem] border-4 border-[#6BB0FF] bg-linear-to-br from-sky-50 via-white to-sky-100 shadow-[0_20px_40px_-20px_rgba(30,64,175,0.35)] lg:max-w-none"
                        >

                            @if ($src !== '')

                                <img
                                    src="{{ $src }}"
                                    alt="{{ $update->text_title }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover"
                                    onerror="this.style.visibility='hidden'"
                                />

                            @else

                                <div
                                    class="flex h-full w-full flex-col items-center justify-center text-center"
                                >
                                    <div
                                        class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur"
                                    >
                                        <svg
                                            class="h-7 w-7 text-[#2F57A8]"
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

                                    <p class="text-sm font-bold uppercase tracking-wide text-[#2F57A8]">
                                        Galeri Event
                                    </p>
                                </div>

                            @endif

                        </div>


                        {{-- Right — text --}}
                        <div class="lg:max-w-2xl">

                            <h3
                                class="text-3xl font-extrabold uppercase leading-[1.1] tracking-tight text-[#1F4E9E] sm:text-4xl lg:text-[2.4rem]"
                            >
                                {{ $update->text_title }}
                            </h3>


                            <div
                                class="mt-6 space-y-5 text-[13px] font-medium leading-snug text-slate-900 sm:text-sm"
                            >

                                @foreach ($paragraphs as $paragraph)

                                    <p class="whitespace-pre-line">
                                        {{ $paragraph }}
                                    </p>

                                @endforeach

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>


    {{-- Tombol panah atas / bawah: pindah antar update --}}
    @if (!$updates->isEmpty() && $updates->count() > 1)
        <div class="fixed right-4 top-1/2 z-40 flex -translate-y-1/2 flex-col gap-3 sm:right-8 lg:right-12">

            <button
                type="button"
                data-vu-up
                aria-label="Update sebelumnya"
                class="flex h-7 w-7 items-center justify-center rounded-md border-2 border-[#10127A] bg-white/90 text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m18 15-6-6-6 6" />
                </svg>
            </button>

            <button
                type="button"
                data-vu-down
                aria-label="Update berikutnya"
                class="flex h-7 w-7 items-center justify-center rounded-md border-2 border-[#10127A] bg-white/90 text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>

        </div>

        <script>
            (function () {
                var items = Array.prototype.slice.call(document.querySelectorAll('[data-vu-item]'));
                var up = document.querySelector('[data-vu-up]');
                var down = document.querySelector('[data-vu-down]');
                var offset = 24; // jarak atas saat berpindah antar update

                if (!items.length || !up || !down) { return; }

                function top(el) {
                    return el.getBoundingClientRect().top + window.scrollY;
                }

                function currentIndex() {
                    var y = window.scrollY + offset + 10;
                    var idx = 0;
                    items.forEach(function (el, i) { if (top(el) <= y) { idx = i; } });
                    return idx;
                }

                function goTo(i) {
                    i = Math.max(0, Math.min(items.length - 1, i));
                    window.scrollTo({ top: top(items[i]) - offset, behavior: 'smooth' });
                }

                up.addEventListener('click', function () { goTo(currentIndex() - 1); });
                down.addEventListener('click', function () { goTo(currentIndex() + 1); });
            })();
        </script>
    @endif

</section>


@if ($standalone)
    {{-- Kalau file induk punya footer: @include('public.footer') --}}
</body>
</html>
@endif