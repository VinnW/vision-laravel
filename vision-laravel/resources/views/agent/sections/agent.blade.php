{{--
    Halaman Agen (publik) → /{username}
    Lokasi saran: resources/views/agent/sections/agent.blade.php

    VARIABEL YANG DITERIMA (dikirim dari route/controller):
      $agent    : object { username, name, bio, image_url, wa_link }
      $isOwner  : bool   → true HANYA bila agen yang sedang login = pemilik halaman ini.
                           Bar & tombol edit dirender di server hanya bila true,
                           jadi customer tidak pernah menerima tombolnya di HTML.
      $editUrl  : (opsional) URL halaman edit
      $products : (opsional) array produk. Bila tidak dikirim, diambil dari tabel `products`
                  dengan cara yang sama seperti products.blade.php

    Section ini dipasang di dalam halaman yang sudah memuat header (seperti section lain di public.*).
--}}

@php
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

    $isOwner     = $isOwner ?? false;
    $username    = (string) ($agent->username ?? '');
    $name        = trim((string) ($agent->name ?? ''));
    $displayName = $name !== '' ? $name : $username;
    $photo       = $imageSrc($agent->image_url ?? null);
    $paragraphs  = preg_split('/\R{2,}/', trim((string) ($agent->bio ?? '')), -1, PREG_SPLIT_NO_EMPTY);
    $shareUrl    = url('/' . $username);
    $editUrl     = $editUrl ?? (Route::has('agent.edit') ? route('agent.edit') : url('/agent/edit'));

    // Link WhatsApp: boleh tersimpan sebagai link penuh atau nomor (08xx / 628xx)
    $wa = trim((string) ($agent->wa_link ?? ''));

    if ($wa !== '' && !preg_match('#^https?://#i', $wa)) {
        $digits = preg_replace('/\D+/', '', $wa);

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        $wa = strlen($digits) >= 9 ? 'https://wa.me/' . $digits : '';
    }

    // Slider produk
    if (!isset($products)) {
        $products = \Illuminate\Support\Facades\DB::table('products')
            ->orderBy('id')
            ->get()
            ->map(fn ($p) => [
                'id'    => $p->id,
                'name'  => $p->text_title,
                'image' => $imageSrc($p->image_url_left),
                'link'  => Route::has('products.detail')
                    ? route('products.detail', ['id' => $p->id])
                    : url('/produk/' . $p->id),
            ])
            ->values()
            ->all();
    }
@endphp

<section
    id="agent"
    x-data="{
        copied: false,
        atStart: true,
        atEnd: false,
        canScroll: false,

        copyLink() {
            navigator.clipboard.writeText(@js($shareUrl)).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },

        update() {
            const r = this.$refs.rail;
            if (!r) return;
            this.canScroll = r.scrollWidth > r.clientWidth + 4;
            this.atStart = r.scrollLeft <= 4;
            this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 4;
        },

        move(dir) {
            const r = this.$refs.rail;
            r.scrollBy({ left: dir * r.clientWidth * 0.8, behavior: 'smooth' });
        },
    }"
    x-init="$nextTick(() => update())"
    @resize.window.debounce.150ms="update()"
    class="relative overflow-hidden bg-white pb-16 pt-28 lg:pb-24 lg:pt-32"
>

    {{-- Pola hexagon dekoratif (kanan bawah) --}}
    <svg
        class="pointer-events-none absolute -right-10 bottom-0 hidden h-[28rem] w-[38rem] text-sky-200/70 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 20%, transparent 95%); mask-image: linear-gradient(to left, black 20%, transparent 95%);"
    >
        <defs>
            <pattern id="agent-hex-br" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#agent-hex-br)" />
    </svg>

    <div class="relative mx-auto max-w-6xl px-6 lg:px-10">

        {{-- ===================================================== --}}
        {{-- BAR PEMILIK: hanya dirender bila agen yang login = pemilik halaman --}}
        {{-- ===================================================== --}}
        @if ($isOwner)
            <div class="mb-10 flex flex-col gap-4 rounded-2xl border-2 border-[#6BB0FF]/60 bg-[#EEF5FF] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-[#10127A]">Ini tampilan halaman Anda</p>
                    <p class="mt-0.5 text-xs text-[#1B2F8F]">
                        Bar ini hanya terlihat oleh Anda. Customer yang membuka link Anda tidak melihatnya.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        @click="copyLink()"
                        class="rounded-full border-2 border-[#10127A] px-5 py-2 text-xs font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95"
                    >
                        <span x-show="!copied">Salin link</span>
                        <span x-show="copied" x-cloak>Link tersalin</span>
                    </button>

                    <a
                        href="{{ $editUrl }}"
                        class="rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-6 py-2 text-xs font-medium text-white shadow-[0_10px_22px_-10px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5"
                    >
                        Edit profil
                    </a>
                </div>
            </div>
        @endif


        {{-- ===================================================== --}}
        {{-- PROFIL --}}
        {{-- ===================================================== --}}
        <div class="grid items-center gap-10 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-14">

            {{-- Foto --}}
            <div class="mx-auto w-full max-w-xs sm:max-w-sm">
                <div class="relative aspect-4/5 overflow-hidden rounded-[2rem] border-4 border-[#6BB0FF] bg-slate-200 shadow-[0_20px_40px_-20px_rgba(30,64,175,0.35)]">
                    @if ($photo !== '')
                        <img
                            src="{{ $photo }}"
                            alt="Foto {{ $displayName }}"
                            decoding="async"
                            class="h-full w-full object-cover object-top"
                        />
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg class="h-20 w-20 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Teks --}}
            <div class="min-w-0">

                <h1 class="break-words text-4xl font-extrabold uppercase leading-[1.05] tracking-tight text-[#10127A] sm:text-5xl lg:text-[3.4rem]">
                    <span class="inline-flex items-center gap-3">
                        Halo
                        <svg class="h-8 w-8 shrink-0 sm:h-10 sm:w-10" viewBox="0 0 24 24" role="img" aria-label="Agen terverifikasi">
                            <circle cx="12" cy="12" r="11" fill="#2F8CFF" />
                            <path d="m7.5 12.3 3 3 6-6.3" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <br>
                    Saya {{ $displayName }}
                </h1>

                <p class="mt-3 text-base font-bold uppercase tracking-wide text-[#10127A] sm:text-lg">
                    Official Trusted Agent Vision
                </p>

                <div class="mt-5 max-w-xl space-y-4 text-sm leading-relaxed text-slate-800">
                    @forelse ($paragraphs as $para)
                        <p class="whitespace-pre-line">{{ trim($para) }}</p>
                    @empty
                        @if ($isOwner)
                            <p class="rounded-xl border-2 border-dashed border-[#6BB0FF]/60 px-4 py-3 text-[#1B2F8F]">
                                Pengalaman Anda belum diisi. Tambahkan lewat tombol Edit profil.
                            </p>
                        @endif
                    @endforelse
                </div>

            </div>
        </div>


        {{-- ===================================================== --}}
        {{-- SLIDER PRODUK (3 kartu terlihat, geser untuk melihat sisanya) --}}
        {{-- ===================================================== --}}
        @if (count($products) > 0)
            <div class="mt-14 lg:mt-16">

                <div
                    x-ref="rail"
                    @scroll.passive="update()"
                    class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                >
                    @foreach ($products as $product)
                        <a
                            href="{{ $product['link'] }}"
                            class="group relative aspect-4/3 w-[78%] shrink-0 snap-start overflow-hidden rounded-3xl border-4 border-[#6BB0FF] bg-slate-200 shadow-[0_20px_40px_-24px_rgba(30,64,175,0.35)] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#1E3A8A]/40 sm:w-[calc((100%-2rem)/3)]"
                        >
                            @if (!empty($product['image']))
                                <img
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['name'] }}"
                                    loading="lazy"
                                    decoding="async"
                                    draggable="false"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <path d="m21 15-5-5-9 9" />
                                    </svg>
                                </div>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-white/95 via-white/70 to-transparent px-4 pb-3 pt-10">
                                <p class="text-xs font-extrabold uppercase leading-tight text-[#10127A] sm:text-[13px]">
                                    {{ $product['name'] }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Panah (hanya muncul bila produk lebih dari yang muat) --}}
                <div x-show="canScroll" x-cloak class="mt-4 flex items-center justify-center gap-4">
                    <button
                        type="button"
                        @click="move(-1)"
                        :disabled="atStart"
                        aria-label="Produk sebelumnya"
                        class="flex h-7 w-7 items-center justify-center rounded-md border-2 border-[#10127A] text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-[#10127A]"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6" /></svg>
                    </button>

                    <button
                        type="button"
                        @click="move(1)"
                        :disabled="atEnd"
                        aria-label="Produk berikutnya"
                        class="flex h-7 w-7 items-center justify-center rounded-md border-2 border-[#10127A] text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white active:scale-95 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-[#10127A]"
                    >
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>

            </div>
        @endif


        {{-- ===================================================== --}}
        {{-- AJAKAN KONSULTASI --}}
        {{-- ===================================================== --}}
        <div class="mt-10 max-w-xl">
            <p class="text-sm font-medium leading-snug text-slate-800">
                Konsultasikan kebutuhan asuransi Anda bersama saya.<br>
                Hubungi saya di
            </p>

            @if ($wa !== '')
                <a
                    href="{{ $wa }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-4 inline-flex items-center gap-2 rounded-full bg-linear-to-r from-[#2FA45A] to-[#1E8A47] px-9 py-3 text-sm font-medium text-white shadow-[0_10px_22px_-10px_rgba(30,138,71,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_26px_-10px_rgba(30,138,71,0.8)]"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.3A8.4 8.4 0 1 1 21 11.5Z" /></svg>
                    Hubungi Kami
                </a>
            @elseif ($isOwner)
                <p class="mt-4 inline-block rounded-xl border-2 border-dashed border-[#6BB0FF]/60 px-4 py-3 text-xs text-[#1B2F8F]">
                    Link WhatsApp belum diisi, tombol Hubungi Kami belum tampil untuk customer.
                </p>
            @endif
        </div>

    </div>
</section>