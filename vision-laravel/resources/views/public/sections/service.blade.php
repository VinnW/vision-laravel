{{--
    Service Section
    - Blok atas: icon + "Visual Image Desain / Our Service" (kiri) + 2 paragraf
      intro (kanan) — full-bleed, tanpa rounded corner, sesuai wireframe.
    - Blok bawah: grid 2 kolom full-bleed, bergantian tone gelap/terang, tiap
      kolom = "gambar yang ditimpa tulisan" (background image + overlay warna
      + konten di atasnya). Salah satu kolom punya CTA yang membuka modal
      kalkulator sederhana.
    - Tailwind CSS + Alpine.js. $intro, $services dikirim dari controller;
      fallback disediakan agar section tetap tampil standalone.
    - 'image' di tiap service opsional — kalau kosong akan tampil sebagai
      blok warna polos (tanpa dependensi internet), bukan foto asli.
--}}
@php
    $intro ??= [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo',
    ];

    $services ??= [
        [
            'tone'       => 'dark',
            'eyebrow'    => "Visual Image Desain\nZurix",
            'heading'    => 'Zuric',
            'image'      => null,
            'paragraphs' => [
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo',
            ],
        ],
        [
            'tone'    => 'light',
            'eyebrow' => "Visual Image Desain\nKalkulator",
            'heading' => "Akumulasikan\nPerlindungan Anda",
            'image'   => null,
            'subtitle' => 'Smart Calculator',
            'action'  => [
                'label' => 'Mulai Menghitung',
                'type'  => 'calculator',
            ],
        ],
    ];
@endphp

<div x-data="{ calculatorOpen: false, nilai: 100000000, durasi: 10, get estimasi() { return this.durasi > 0 ? Math.round((this.nilai * 0.012 * this.durasi)) : 0 } }">

    {{-- ============ Top intro block ============ --}}
    <section class="relative overflow-hidden bg-linear-to-br from-slate-100 via-slate-50 to-indigo-50 py-20 lg:py-28">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -left-24 top-1/3 h-96 w-96 rounded-full bg-indigo-200/25 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-16 h-80 w-80 rounded-full bg-[#F2A93B]/15 blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-6 lg:grid-cols-2 lg:gap-20 lg:px-10">

            {{-- Left — icon + heading --}}
            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur">
                    <svg class="h-8 w-8 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="m21 15-5-5-9 9" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">
                    Visual Image Desain<br />Our Service
                </h1>
            </div>

            {{-- Right — intro paragraphs --}}
            <div class="space-y-5 text-[15px] font-medium leading-relaxed text-slate-600">
                @foreach ($intro as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ Bottom split service blocks ============ --}}
    <section class="grid grid-cols-1 md:grid-cols-2">
        @foreach ($services as $service)
            @php
                $isDark = ($service['tone'] ?? 'light') === 'dark';
            @endphp

            <div
                @if(!empty($service['image']))
                    style="background-image: linear-gradient({{ $isDark ? 'rgba(15,23,42,.72),rgba(15,23,42,.72)' : 'rgba(255,255,255,.82),rgba(255,255,255,.82)' }}), url('{{ $service['image'] }}'); background-size: cover; background-position: center;"
                @endif
                class="relative flex min-h-[30rem] flex-col justify-between px-8 py-12 lg:min-h-[34rem] lg:px-14 lg:py-16 {{ $isDark ? 'bg-slate-500 text-white' : 'bg-slate-100 text-slate-900' }}"
            >
                {{-- Eyebrow: icon + label --}}
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $isDark ? 'bg-white/15' : 'bg-white' }} shadow-sm">
                        <svg class="h-6 w-6 {{ $isDark ? 'text-white' : 'text-slate-600' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="m21 15-5-5-9 9" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold leading-snug {{ $isDark ? 'text-white/90' : 'text-slate-700' }}">
                        {!! nl2br(e($service['eyebrow'])) !!}
                    </p>
                </div>

                {{-- Heading --}}
                <h2 class="my-10 text-center text-3xl font-extrabold uppercase leading-tight sm:text-4xl">
                    {!! nl2br(e($service['heading'])) !!}
                </h2>

                {{-- Footer: either descriptive paragraphs, or subtitle + CTA --}}
                <div>
                    @if (!empty($service['paragraphs']))
                        <div class="space-y-4 text-[15px] font-medium leading-relaxed {{ $isDark ? 'text-white/85' : 'text-slate-600' }}">
                            @foreach ($service['paragraphs'] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($service['subtitle']) || !empty($service['action']))
                        <div class="text-center">
                            @if (!empty($service['subtitle']))
                                <p class="text-sm font-bold uppercase tracking-wide {{ $isDark ? 'text-white/80' : 'text-slate-700' }}">
                                    {{ $service['subtitle'] }}
                                </p>
                            @endif

                            @if (!empty($service['action']))
                                @if ($service['action']['type'] === 'calculator')
                                    <button
                                        type="button"
                                        @click="calculatorOpen = true"
                                        class="mt-3 text-sm font-semibold uppercase tracking-wide text-slate-400 underline decoration-slate-300 underline-offset-4 transition-colors duration-200 hover:text-[#F2A93B] hover:decoration-[#F2A93B]"
                                    >
                                        {{ $service['action']['label'] }}
                                    </button>
                                @else
                                    <a
                                        href="{{ $service['action']['href'] ?? '#' }}"
                                        class="mt-3 inline-block text-sm font-semibold uppercase tracking-wide text-slate-400 underline decoration-slate-300 underline-offset-4 transition-colors duration-200 hover:text-[#F2A93B] hover:decoration-[#F2A93B]"
                                    >
                                        {{ $service['action']['label'] }}
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </section>

    {{-- ============ Calculator modal ============ --}}
    <div
        x-show="calculatorOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-6 backdrop-blur-sm"
        style="display: none;"
        @keydown.escape.window="calculatorOpen = false"
    >
        <div
            @click.outside="calculatorOpen = false"
            x-show="calculatorOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md overflow-hidden rounded-[2rem] bg-white p-8 shadow-2xl"
        >
            <button
                @click="calculatorOpen = false"
                type="button"
                aria-label="Tutup kalkulator"
                class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>

            <h3 class="text-xl font-extrabold text-slate-900">Smart Calculator</h3>
            <p class="mt-1 text-sm font-medium text-slate-500">Hitung estimasi perlindungan Anda.</p>

            <div class="mt-6 space-y-5">
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Nilai Pertanggungan (Rp)</span>
                    <input
                        type="number"
                        x-model.number="nilai"
                        min="0"
                        step="1000000"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none transition-colors focus:border-[#F2A93B]"
                    />
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-slate-700">Durasi (tahun)</span>
                    <input
                        type="number"
                        x-model.number="durasi"
                        min="1"
                        max="30"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 outline-none transition-colors focus:border-[#F2A93B]"
                    />
                </label>

                {{-- NOTE: formula di atas (nilai * 0.012 * durasi) hanyalah dummy —
                     ganti sesuai perhitungan bisnis Anda yang sebenarnya. --}}
                <div class="rounded-2xl bg-slate-50 px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Estimasi Premi</p>
                    <p class="mt-1 text-2xl font-extrabold text-slate-900" x-text="'Rp ' + estimasi.toLocaleString('id-ID')"></p>
                </div>
            </div>
        </div>
    </div>
</div>