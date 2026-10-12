{{--
    Header
    - Transparan di atas Hero (fixed), berubah putih blur saat di-scroll
    - Logo kiri, menu tengah (biru), tombol Login gradasi + ikon globe di kanan
    - Fungsi (route, dropdown bahasa, menu mobile) tidak diubah
--}}

@php
    $navItems = [
        ['label' => 'About Us', 'section' => 'about'],
        ['label' => 'Products', 'section' => 'products'],
        ['label' => 'Service', 'section' => 'service'],
        ['label' => 'Event', 'section' => 'event'],
        ['label' => 'Contact Us', 'section' => 'contact'],
    ];

    // Jika ada file logo di public/images/logo-vision.png, otomatis dipakai.
    $logoPath = 'images/logo-vision.png';
    $hasLogo = file_exists(public_path($logoPath));
@endphp

<header
    x-data="{ mobileOpen: false, langOpen: false, scrolled: false }"
    @keydown.escape.window="mobileOpen = false; langOpen = false"
    @scroll.window="scrolled = window.scrollY > 20"
    :class="(scrolled || mobileOpen) ? 'bg-white/90 shadow-sm backdrop-blur-xl' : 'bg-transparent'"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
>
    <div class="mx-auto flex h-24 max-w-7xl items-center justify-between px-6 lg:px-10">

        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            class="shrink-0 transition-opacity hover:opacity-80"
            aria-label="{{ config('app.name', 'Vision Community') }}"
        >
            @if ($hasLogo)
                <img
                    src="{{ asset($logoPath) }}"
                    alt="{{ config('app.name', 'Vision Community') }}"
                    class="h-14 w-auto"
                />
            @else
                <div class="relative flex flex-col items-center leading-none">
                    {{-- Bintang --}}
                    <svg class="absolute -top-3 left-[34%] h-4 w-4 text-[#F2A93B]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 0c.6 6.2 5.8 11.4 12 12-6.2.6-11.4 5.8-12 12-.6-6.2-5.8-11.4-12-12C6.2 11.4 11.4 6.2 12 0z" />
                    </svg>

                    <div class="w-full border-t border-[#1E3A8A]"></div>
                    <span class="font-serif text-[34px] font-extrabold tracking-wide text-[#1E3A8A]">VISION</span>
                    <div class="w-full border-t border-[#1E3A8A]"></div>
                    <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.35em] text-[#1E3A8A]">Community</span>
                    <div class="mt-0.5 w-full border-t border-[#1E3A8A]"></div>
                    <span class="mt-0.5 text-[7px] font-medium text-[#1E3A8A]">Satu Visi Satu <span class="text-red-500">&#10084;</span></span>
                </div>
            @endif
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-9 lg:flex">
            @foreach ($navItems as $item)
                <a
                    href="#{{ $item['section'] }}"
                    class="group relative text-[13px] font-bold uppercase tracking-wide text-[#1F4FD1] transition-colors hover:text-[#1E3A8A]"
                >
                    {{ $item['label'] }}
                    <span class="absolute -bottom-1.5 left-0 h-0.5 w-0 bg-[#F2A93B] transition-all duration-300 ease-out group-hover:w-full"></span>
                </a>
            @endforeach
        </nav>

        {{-- Right actions --}}
        <div class="hidden items-center gap-4 lg:flex">

            {{-- Login --}}
            <a
                href="{{ route('admin.login.page') }}"
                class="rounded-full bg-linear-to-r from-[#2E6FD8] to-[#5BB4E8] px-9 py-2 text-sm font-medium text-white shadow-[0_8px_20px_-8px_rgba(46,111,216,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_12px_24px_-8px_rgba(46,111,216,0.8)]"
            >
                Login
            </a>

            {{-- Language dropdown (ikon globe) --}}
            <div class="relative" @click.outside="langOpen = false">
                <button
                    @click="langOpen = !langOpen"
                    type="button"
                    class="flex h-11 w-11 items-center justify-center rounded-full text-[#2E6FD8] transition-all duration-200 hover:bg-white/60"
                    :aria-expanded="langOpen.toString()"
                    aria-label="Language"
                >
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                        <path d="M4.5 6.5c2.2 1 4.7 1.5 7.5 1.5s5.3-.5 7.5-1.5M4.5 17.5c2.2-1 4.7-1.5 7.5-1.5s5.3.5 7.5 1.5" />
                    </svg>
                </button>

                <div
                    x-show="langOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-xl shadow-slate-900/5"
                    style="display: none;"
                >
                    <button type="button" class="block w-full px-4 py-2 text-left text-sm font-medium text-slate-600 hover:bg-slate-50">Bahasa Indonesia</button>
                    <button type="button" class="block w-full px-4 py-2 text-left text-sm font-medium text-slate-600 hover:bg-slate-50">English</button>
                </div>
            </div>
        </div>

        {{-- Mobile menu toggle --}}
        <button
            @click="mobileOpen = !mobileOpen"
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-full text-[#1F4FD1] lg:hidden"
            :aria-expanded="mobileOpen.toString()"
            aria-label="Buka menu"
        >
            <svg x-show="!mobileOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
            <svg x-show="mobileOpen" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display: none;"><path d="M6 6l12 12M18 6 6 18" /></svg>
        </button>
    </div>

    {{-- Mobile nav panel --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="border-t border-slate-200/70 bg-white/95 backdrop-blur-xl lg:hidden"
        style="display: none;"
    >
        <nav class="flex flex-col gap-1 px-6 py-4">
            @foreach ($navItems as $item)
                <a
                    href="#{{ $item['section'] }}"
                    @click="mobileOpen = false"
                    class="rounded-xl px-3 py-2.5 text-sm font-bold uppercase tracking-wide text-[#1F4FD1] hover:bg-slate-50 hover:text-[#1E3A8A]"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="mt-3 flex items-center gap-3 border-t border-slate-200/70 pt-4">
                <a href="{{ route('admin.login.page') }}" class="flex-1 rounded-full bg-linear-to-r from-[#2E6FD8] to-[#5BB4E8] px-5 py-2.5 text-center text-sm font-medium text-white">
                    Login
                </a>
                <button type="button" class="flex-1 rounded-full border border-[#2E6FD8]/40 px-5 py-2.5 text-sm font-semibold text-[#2E6FD8]">
                    Language
                </button>
            </div>
        </nav>
    </div>
</header>

{{--
    NOTE — Font (opsional, agar lebih mirip desain):
    Tambahkan font geometris seperti "Poppins" / "Plus Jakarta Sans" di layout,
    lalu di tailwind.config.js / @theme:

    fontFamily: { sans: ['"Poppins"', 'sans-serif'] }
--}}