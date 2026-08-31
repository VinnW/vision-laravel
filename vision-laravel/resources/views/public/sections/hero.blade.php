{{--
    Hero Section
    - Mesh-gradient background with soft blurred orbs (replaces the flat grey
      placeholder box), a floating glass content card, and interactive slide dots
    - Tailwind CSS + Alpine.js
    - $slides is expected from the controller/component; a fallback is provided
      below so the section still renders standalone.
--}}
@php
    $slides ??= [
        [
            'title'    => "Desain Banner\nHalaman Utama",
            'subtitle' => '5 slide desain gambar otomatis dan manual slide,',
            'image'    => null,
        ],
    ];
@endphp

<section
    x-data="{ active: 0, total: {{ count($slides) }}, timer: null }"
    x-init="
        timer = setInterval(() => { active = (active + 1) % total }, 5000)
    "
    class="relative overflow-hidden bg-slate-50 pb-24 pt-14 lg:pb-32 lg:pt-20"
>
    {{-- Decorative mesh-gradient orbs — purely ambient, ignored by assistive tech --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 -top-32 h-96 w-96 rounded-full bg-[#F2A93B]/25 blur-3xl"></div>
        <div class="absolute -right-20 top-10 h-112 w-md-112 rounded-full bg-indigo-400/20 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 h-80 w-80 rounded-full bg-sky-300/20 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-10">

        {{-- Hero panel --}}
        <div class="relative overflow-hidden rounded-[2.5rem] border border-white/60 bg-linear-to-br from-[#EDE7FB] via-[#E9F0FF] to-[#FFF3E2] shadow-[0_30px_80px_-30px_rgba(30,41,59,0.25)]">

            {{-- subtle inner sheen --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(120%_120%_at_50%_0%,rgba(255,255,255,0.65),transparent_60%)]" aria-hidden="true"></div>

            <div class="relative flex min-h-104 flex-col items-center justify-center px-6 py-20 text-center lg:min-h-120 lg:px-16">

                @foreach ($slides as $index => $slide)
                    <div
                        x-show="active === {{ $index }}"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="flex flex-col items-center"
                        @if($index > 0) style="display: none;" @endif
                    >
                        {{-- Icon / image --}}
                        <div class="mb-8 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/70 shadow-sm backdrop-blur">
                            @if (!empty($slide['image']))
                                <img src="{{ $slide['image'] }}" alt="" class="h-9 w-9 object-contain" />
                            @else
                                <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path d="m21 15-5-5-9 9" />
                                </svg>
                            @endif
                        </div>

                        {{-- Headline --}}
                        <h1 class="max-w-2xl text-3xl font-extrabold leading-tight text-indigo-700 sm:text-4xl lg:text-[2.75rem]">
                            {!! nl2br(e($slide['title'])) !!}
                        </h1>

                        {{-- Subtitle --}}
                        <p class="mt-5 max-w-xl text-lg leading-relaxed text-indigo-500/80">
                            {{ $slide['subtitle'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Slide indicators --}}
            @if (count($slides) > 1)
                <div class="relative z-10 flex items-center justify-center gap-2 pb-8">
                    @foreach ($slides as $index => $slide)
                        <button
                            type="button"
                            @click="active = {{ $index }}; clearInterval(timer); timer = setInterval(() => { active = (active + 1) % total }, 5000)"
                            :class="active === {{ $index }} ? 'w-6 bg-indigo-600' : 'w-2 bg-white/80 hover:bg-white'"
                            class="h-2 rounded-full shadow-sm transition-all duration-300"
                            :aria-current="active === {{ $index }}"
                            aria-label="Slide {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="relative z-10 -mt-7 flex flex-col items-center justify-center gap-4 sm:flex-row">
            <a
                href="{{ Route::has('products') ? route('products') : '#' }}"
                class="w-full rounded-full bg-white px-8 py-4 text-center text-sm font-semibold text-slate-700 shadow-[0_18px_40px_-15px_rgba(30,41,59,0.35)] ring-1 ring-slate-200/70 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_45px_-15px_rgba(30,41,59,0.4)] sm:w-auto sm:min-w-60"
            >
                Produk Layanan
            </a>
            <a
                href="{{ Route::has('join-agent') ? route('join-agent') : '#' }}"
                class="w-full rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-8 py-4 text-center text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_45px_-15px_rgba(232,130,60,0.65)] sm:w-auto sm:min-w-60"
            >
                Join Agent
            </a>
        </div>
    </div>
</section>