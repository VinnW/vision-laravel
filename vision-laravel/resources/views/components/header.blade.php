<header
    x-data="{ mobileOpen: false, langOpen: false }"
    @keydown.escape.window="mobileOpen = false; langOpen = false"
    class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/75 backdrop-blur-xl backdrop-saturate-150"
>
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6 lg:px-10">

        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            class="shrink-0 text-xl font-extrabold tracking-tight text-[#F2A93B] transition-opacity hover:opacity-80"
        >
            {{ config('app.name', 'Logo Perusahaan') }}
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-9 lg:flex">
            @php
                $navItems = [
                    ['label' => 'About Us', 'route' => 'about'],
                    ['label' => 'Products', 'route' => 'products'],
                    ['label' => 'Service', 'route' => 'service'],
                    ['label' => 'Event', 'route' => 'event'],
                    ['label' => 'Contact Us', 'route' => 'contact'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a
                    href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="group relative text-[13px] font-semibold uppercase tracking-wide text-slate-600 transition-colors hover:text-slate-900"
                >
                    {{ $item['label'] }}
                    <span class="absolute -bottom-1.5 left-0 h-0.5 w-0 bg-[#F2A93B] transition-all duration-300 ease-out group-hover:w-full"></span>
                </a>
            @endforeach
        </nav>

        {{-- Right actions --}}
        <div class="hidden items-center gap-3 lg:flex">

            {{-- Login --}}
            <a
                href="{{ route('login') }}"
                class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50/80 px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all duration-200 hover:border-slate-300 hover:bg-slate-100"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                Login
            </a>

            {{-- Language dropdown --}}
            <div class="relative" @click.outside="langOpen = false">
                <button
                    @click="langOpen = !langOpen"
                    type="button"
                    class="flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50"
                    :aria-expanded="langOpen.toString()"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" />
                    </svg>
                    Language
                    <svg
                        class="h-3.5 w-3.5 transition-transform duration-200"
                        :class="langOpen ? 'rotate-180' : ''"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    >
                        <path d="m6 9 6 6 6-6" />
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
                    class="absolute right-0 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-xl shadow-slate-900/5"
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
            class="flex h-10 w-10 items-center justify-center rounded-full text-slate-700 lg:hidden"
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
                    href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="rounded-xl px-3 py-2.5 text-sm font-semibold uppercase tracking-wide text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="mt-3 flex items-center gap-3 border-t border-slate-200/70 pt-4">
                <a href="{{ route('login') }}" class="flex-1 rounded-full bg-slate-900 px-5 py-2.5 text-center text-sm font-semibold text-white">
                    Login
                </a>
                <button type="button" class="flex-1 rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700">
                    Language
                </button>
            </div>
        </nav>
    </div>
</header>

{{--
    NOTE — Tailwind theme tokens (opsional):
    Kalau mau warna & font konsisten dengan file hero.blade.php, tambahkan di
    tailwind.config.js:

    theme: {
      extend: {
        fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
        colors: {
          brand: { DEFAULT: '#F2A93B', dark: '#DC8A1E' },
          ink:   '#14181F',
        },
      },
    },
--}}