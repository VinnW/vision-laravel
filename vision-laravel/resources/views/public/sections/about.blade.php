{{--
    About / Company Profile Article
    - Two-column layout: soft gradient image panel on the left, article copy +
      social links on the right — mirrors the wireframe, restyled to match
      header.blade.php / hero.blade.php (mesh-gradient, rounded panel, amber
      + indigo accents).
    - Tailwind CSS. $image and $paragraphs are optional and come from the
      controller; sensible fallbacks are provided so the section still
      renders standalone.
--}}
@php
    $image ??= null;

    $paragraphs ??= [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
        'Maecenas accumsan lacus vel facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
    ];

    $socials = [
        ['label' => 'TikTok', 'url' => '#'],
        ['label' => 'Instagram', 'url' => '#'],
        ['label' => 'Facebook', 'url' => '#'],
        ['label' => 'YouTube', 'url' => '#'],
    ];
@endphp

<section class="relative overflow-hidden bg-slate-50 py-20 lg:py-28">

    {{-- Decorative mesh-gradient orbs, consistent with the hero section --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -left-24 top-1/4 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
        <div class="absolute -right-20 -bottom-24 h-112 w-md-112 rounded-full bg-[#F2A93B]/15 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 lg:px-10">
        <div class="grid items-stretch gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">

            {{-- Left — image panel --}}
            <div class="relative overflow-hidden rounded-[2.5rem] border border-white/60 bg-linear-to-br from-slate-100 via-white to-slate-200 shadow-[0_30px_80px_-30px_rgba(30,41,59,0.2)]">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(120%_120%_at_20%_10%,rgba(255,255,255,0.8),transparent_60%)]" aria-hidden="true"></div>

                <div class="relative flex min-h-90 flex-col items-center justify-center px-8 py-16 text-center lg:min-h-full">
                    @if (!empty($image))
                        <img
                            src="{{ $image }}"
                            alt="{{ config('app.name', 'Perusahaan') }} — company profile"
                            class="h-full w-full rounded-4xl object-cover"
                        />
                    @else
                        <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/80 shadow-sm backdrop-blur">
                            <svg class="h-8 w-8 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <path d="m21 15-5-5-9 9" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold uppercase tracking-wide text-indigo-600">
                            Image<br />Desain Visual
                        </p>
                    @endif
                </div>
            </div>

            {{-- Right — article --}}
            <div class="flex flex-col justify-between">
                <div>
                    <p class="text-2xl font-extrabold tracking-tight text-[#F2A93B] sm:text-3xl">
                        {{ config('app.name', 'Logo Perusahaan') }}
                    </p>

                    <h1 class="mt-3 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl">
                        Company Profile Article
                    </h1>

                    <div class="mt-8 space-y-6 text-[15px] font-medium leading-relaxed text-slate-700">
                        @foreach ($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

                {{-- Social links --}}
                <div class="mt-10 flex flex-wrap items-center gap-4 border-t border-slate-200/70 pt-8">
                    <span class="text-sm font-semibold text-slate-400">Terhubung dengan kami</span>

                    <div class="flex items-center gap-3">
                        @foreach ($socials as $social)
                            <a
                                href="{{ $social['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $social['label'] }}"
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-[#F2A93B]/40 hover:text-[#F2A93B] hover:shadow-md"
                            >
                                @switch($social['label'])
                                    @case('TikTok')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 5.82c-.7-.77-1.14-1.76-1.24-2.82H12.4v13.44c0 1.48-1.2 2.68-2.68 2.68a2.68 2.68 0 0 1 0-5.36c.28 0 .55.04.8.12V10.9a5.6 5.6 0 0 0-.8-.06 5.62 5.62 0 1 0 5.62 5.62V8.9a8.36 8.36 0 0 0 4.86 1.55V7.5a5.7 5.7 0 0 1-3.6-1.68Z" /></svg>
                                        @break
                                    @case('Instagram')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" /></svg>
                                        @break
                                    @case('Facebook')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.6h2.55l.38-2.96h-2.93V8.55c0-.86.24-1.44 1.47-1.44h1.57V4.46A21 21 0 0 0 14.3 4.3c-2.24 0-3.78 1.37-3.78 3.87v2.27H8v2.96h2.52V21h3Z" /></svg>
                                        @break
                                    @case('YouTube')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 7.6a2.7 2.7 0 0 0-1.9-1.9C18 5.2 12 5.2 12 5.2s-6 0-7.7.5A2.7 2.7 0 0 0 2.4 7.6 28 28 0 0 0 2 12a28 28 0 0 0 .4 4.4 2.7 2.7 0 0 0 1.9 1.9c1.7.5 7.7.5 7.7.5s6 0 7.7-.5a2.7 2.7 0 0 0 1.9-1.9c.3-1.45.4-2.93.4-4.4a28 28 0 0 0-.4-4.4ZM10 15V9l5.2 3-5.2 3Z" /></svg>
                                        @break
                                @endswitch
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>