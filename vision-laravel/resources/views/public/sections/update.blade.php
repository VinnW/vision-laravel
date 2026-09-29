@php
    /*
    |----------------------------------------------------------------------
    | Data
    |----------------------------------------------------------------------
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vision Update — {{ config('app.name') }}</title>

    {{-- WAJIB: samakan dengan <head> di file induk yang memuat about.blade.php --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 antialiased">

    {{-- Kalau file induk punya header/navbar, include di sini, misalnya: --}}
    {{-- @include('public.header') --}}
@endif

{{-- Full page: setinggi layar, tanpa batas lebar --}}
<section id="vision-update" class="relative min-h-screen w-full overflow-hidden bg-slate-50 py-12 lg:py-16">

    {{-- Decorative mesh-gradient orbs, consistent with the about section --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 top-1/4 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
        <div class="absolute -right-20 -bottom-24 h-112 w-112 rounded-full bg-[#F2A93B]/15 blur-3xl"></div>
    </div>

    {{-- Lebar penuh, padding samping ikut membesar di layar lebar --}}
    <div class="relative w-full px-6 sm:px-10 lg:px-16 xl:px-24 2xl:px-32">

        {{-- Tombol kembali --}}
        <a
            href="{{ $backUrl }}"
            class="group mb-6 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:-translate-x-0.5 hover:border-[#F2A93B]/40 hover:text-[#F2A93B] hover:shadow-md"
        >
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m15 18-6-6 6-6" />
            </svg>
            Kembali
        </a>

        {{-- Heading --}}
        <h2 class="text-4xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
            Vision Update
        </h2>

        <div class="mt-3 h-1.5 w-20 rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C]"></div>


        @if ($updates->isEmpty())

            {{-- Empty state --}}
            <div class="mt-12 rounded-4xl border-2 border-dashed border-slate-200 bg-white/70 py-20 text-center backdrop-blur">
                <svg class="mx-auto mb-4 h-10 w-10 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="14" rx="2" />
                    <circle cx="8.5" cy="9.5" r="1.5" />
                    <path d="m21 16-5-5-9 9" />
                </svg>

                <p class="text-sm font-medium text-slate-500">Belum ada Vision Update.</p>
            </div>

        @else

            {{-- List (tinggi mengikuti layar, bisa digulir di layar besar) --}}
            <div class="mt-8">

                <div
                    data-vu-scroll
                    class="vu-scroll space-y-12 lg:h-[calc(100vh-24rem)] lg:min-h-104 lg:space-y-16 lg:overflow-y-auto lg:pr-6"
                >
                    @foreach ($updates as $update)
                        @php
                            $src        = $imageSrc($update->image_url ?? '');
                            $paragraphs = $toParagraphs($update->text_body ?? '');
                        @endphp

                        <article class="grid items-start gap-6 lg:grid-cols-2 lg:gap-12">

                            {{-- Left — image --}}
                            <div class="relative aspect-41/28 overflow-hidden rounded-[2.5rem] border border-white/60 bg-linear-to-br from-slate-100 via-white to-slate-200 shadow-[0_30px_80px_-30px_rgba(30,41,59,0.2)]">

                                @if ($src !== '')
                                    <img
                                        src="{{ $src }}"
                                        alt="{{ $update->text_title }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover"
                                        onerror="this.style.visibility='hidden'"
                                    />
                                @else
                                    <div class="flex h-full w-full flex-col items-center justify-center text-center">
                                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur">
                                            <svg class="h-7 w-7 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <rect x="3" y="4" width="18" height="14" rx="2" />
                                                <circle cx="8.5" cy="9.5" r="1.5" />
                                                <path d="m21 16-5-5-9 9" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">Galeri Event</p>
                                    </div>
                                @endif

                            </div>

                            {{-- Right — text --}}
                            <div>
                                <h3 class="text-3xl font-extrabold uppercase leading-tight tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                                    {{ $update->text_title }}
                                </h3>

                                <div class="mt-6 space-y-5 text-base font-medium leading-relaxed text-slate-700 lg:text-lg">
                                    @foreach ($paragraphs as $paragraph)
                                        <p class="whitespace-pre-line">{{ $paragraph }}</p>
                                    @endforeach
                                </div>
                            </div>

                        </article>
                    @endforeach
                </div>

                {{-- Scroll-down button (like the wireframe) --}}
                <div class="mt-4 hidden justify-end lg:flex">
                    <button
                        type="button"
                        data-vu-next
                        aria-label="Gulir ke bawah"
                        class="invisible flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-[#F2A93B]/40 hover:text-[#F2A93B] hover:shadow-md"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                </div>

            </div>

        @endif

    </div>

    <style>
        .vu-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .vu-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .vu-scroll::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 9999px;
        }

        .vu-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 9999px;
        }

        .vu-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>

    <script>
        (function () {
            const root     = document.getElementById('vision-update');
            if (!root) return;

            const scroller = root.querySelector('[data-vu-scroll]');
            const button   = root.querySelector('[data-vu-next]');
            if (!scroller || !button) return;

            // Tombol hanya tampil bila masih ada konten di bawah
            const sync = () => {
                const remaining =
                    scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight;

                button.classList.toggle('invisible', remaining <= 8);
            };

            button.addEventListener('click', () => {
                scroller.scrollBy({
                    top: scroller.clientHeight * 0.8,
                    behavior: 'smooth'
                });
            });

            scroller.addEventListener('scroll', sync, { passive: true });
            window.addEventListener('resize', sync);
            window.addEventListener('load', sync);

            sync();
        })();
    </script>

</section>

@if ($standalone)
    {{-- Kalau file induk punya footer: @include('public.footer') --}}
</body>
</html>
@endif