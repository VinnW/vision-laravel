{{--
    Contact Section (partial)
    - Foto konsultan sebagai latar atas (memudar ke putih), judul besar biru tua di kanan
    - Form konsultasi di kiri dengan label kecil, input kotak abu-abu, tombol "Kirim" pill navy
    - Ikon sosial media bulat navy di kiri bawah form
    - Fungsi form (action, @csrf, old(), @error, session success) TIDAK diubah
    - $person_image opsional dikirim dari controller (dipakai sebagai gambar latar)
    - Dipakai baik di homepage (index.blade.php) maupun di halaman
      standalone contact.blade.php via @include
--}}
@php
    $person_image ??= null;

    $bgImage = $person_image ?: asset('storage/image_products/zuric-contact.jpg');

    $socials ??= [
        ['label' => 'Instagram', 'url' => '#'],
        ['label' => 'Facebook', 'url' => '#'],
        ['label' => 'Twitter', 'url' => '#'],
        ['label' => 'WhatsApp', 'url' => '#'],
    ];

    // Kelas input & label dipakai berulang agar konsisten
    $labelClass = 'mb-1 block text-[11px] font-semibold text-slate-800';
    $inputClass = 'w-full rounded-sm border-2 border-slate-300 bg-white/90 px-3 py-1.5 text-sm font-medium text-slate-900 placeholder:text-xs placeholder:font-normal placeholder:text-slate-400 transition-colors focus:border-[#2F57A8] focus:outline-none focus:ring-2 focus:ring-[#2F57A8]/20';
@endphp

<section id="contact" class="relative overflow-hidden bg-white">

    {{-- ========================================================= --}}
    {{-- BACKGROUND FOTO (atas) --}}
    {{-- ========================================================= --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[26rem] overflow-hidden lg:h-[28rem]" aria-hidden="true">

        <img
            src="{{ $bgImage }}"
            alt=""
            class="h-full w-full object-cover object-[75%_center]"
            onerror="this.style.display='none'"
        >

        {{-- Pudar putih: kiri --}}
        <div class="absolute inset-0 bg-linear-to-r from-white from-25% via-white/60 via-55% to-transparent"></div>

        {{-- Pudar putih: atas (area header) --}}
        <div class="absolute inset-x-0 top-0 h-40 bg-linear-to-b from-white via-white/70 to-transparent"></div>

        {{-- Pudar putih: bawah --}}
        <div class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-white to-transparent"></div>
    </div>

    {{-- Pola hexagon biru muda (kanan atas) --}}
    <svg
        class="pointer-events-none absolute -right-8 top-24 hidden h-72 w-[30rem] text-sky-300/60 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to left, black 25%, transparent 100%); mask-image: linear-gradient(to left, black 25%, transparent 100%);"
    >
        <defs>
            <pattern id="contact-hex-tr" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#contact-hex-tr)" />
    </svg>

    {{-- Pola hexagon biru muda (kiri bawah) --}}
    <svg
        class="pointer-events-none absolute -left-8 bottom-0 hidden h-96 w-80 text-sky-300/60 md:block"
        aria-hidden="true"
        style="-webkit-mask-image: linear-gradient(to right, black 25%, transparent 100%); mask-image: linear-gradient(to right, black 25%, transparent 100%);"
    >
        <defs>
            <pattern id="contact-hex-bl" width="56" height="98" patternUnits="userSpaceOnUse">
                <path
                    fill="currentColor"
                    fill-rule="evenodd"
                    d="M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z"
                    transform="scale(2)"
                />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#contact-hex-bl)" />
    </svg>


    <div class="relative mx-auto max-w-6xl px-6 pb-8 pt-32 lg:px-10 lg:pb-10 lg:pt-44">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,26rem)_1fr] lg:gap-16">

            {{-- ================= KIRI: Form ================= --}}
            <div class="order-2 lg:order-1">

                {{-- Notifikasi sukses (aktif setelah form disambungkan ke controller) --}}
                @if (session('success'))
                    <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ Route::has('contact.submit') ? route('contact.submit') : '#' }}"
                    method="POST"
                    class="space-y-3.5"
                >
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="{{ $labelClass }}">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="{{ $inputClass }}"
                            placeholder="Masukkan nama lengkap"
                        >
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="{{ $labelClass }}">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            class="{{ $inputClass }}"
                            placeholder="nama@email.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label for="city" class="{{ $labelClass }}">Kota</label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            value="{{ old('city') }}"
                            class="{{ $inputClass }}"
                            placeholder="Kota domisili"
                        >
                        @error('city')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label for="phone" class="{{ $labelClass }}">No Telepon</label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            class="{{ $inputClass }}"
                            placeholder="08xxxxxxxxxx"
                        >
                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label for="message" class="{{ $labelClass }}">Pesan</label>
                        <textarea
                            name="message"
                            id="message"
                            rows="7"
                            class="{{ $inputClass }} resize-none"
                            placeholder="Tulis pertanyaan atau kebutuhan konsultasi kamu di sini"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sosial media + Submit --}}
                    <div class="flex items-center justify-between gap-4 pt-1">

                        <div class="flex items-center gap-3">
                            @foreach ($socials as $social)
                                <a
                                    href="{{ $social['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="{{ $social['label'] }}"
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-[#10127A] text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#1F4FD1]"
                                >
                                    @switch($social['label'])
                                        @case('Instagram')
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" /></svg>
                                            @break
                                        @case('Facebook')
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.6h2.55l.38-2.96h-2.93V8.55c0-.86.24-1.44 1.47-1.44h1.57V4.46A21 21 0 0 0 14.3 4.3c-2.24 0-3.78 1.37-3.78 3.87v2.27H8v2.96h2.52V21h3Z" /></svg>
                                            @break
                                        @case('Twitter')
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.7.3-1.5.5-2.4.7.9-.5 1.5-1.3 1.8-2.3-.8.5-1.7.8-2.6 1-.8-.8-1.8-1.3-3-1.3-2.3 0-4.1 1.8-4.1 4.1 0 .3 0 .6.1.9C8.4 8.8 5.4 7.2 3.4 4.7c-.4.6-.6 1.3-.6 2.1 0 1.4.7 2.7 1.8 3.4-.7 0-1.3-.2-1.9-.5v.1c0 2 1.4 3.6 3.3 4-.3.1-.7.1-1.1.1-.3 0-.5 0-.8-.1.5 1.6 2 2.8 3.8 2.8-1.4 1.1-3.2 1.8-5.1 1.8-.3 0-.7 0-1-.1 1.8 1.2 4 1.8 6.3 1.8 7.5 0 11.7-6.3 11.7-11.7v-.5c.8-.6 1.5-1.3 2.1-2.1z" /></svg>
                                            @break
                                        @case('WhatsApp')
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.7.1-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.8-.8-1.4-1.7-1.6-2-.2-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5-.1-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.3zM12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2z" /></svg>
                                            @break
                                    @endswitch
                                </a>
                            @endforeach
                        </div>

                        <button
                            type="submit"
                            class="inline-flex min-w-[7.25rem] items-center justify-center rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-2 text-sm font-medium text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_28px_-12px_rgba(27,52,120,0.85)] sm:min-w-[11rem]"
                        >
                            Kirim
                        </button>

                    </div>
                </form>
            </div>

            {{-- ================= KANAN: Judul + Deskripsi ================= --}}
            <div class="order-1 lg:order-2 lg:mt-24">
                <h1 class="text-4xl font-extrabold uppercase leading-[1.08] tracking-tight text-[#1F4E9E] sm:text-5xl lg:text-[3.4rem]">
                    Konsultasikan<br>Bersama Kami
                </h1>

                <p class="mt-8 text-[13px] font-medium leading-snug text-[#1B2F8F]">
                    Konsultasikan Kebutuhan Anda Bersama Vision
                </p>

                <p class="mt-5 max-w-md text-xs font-medium leading-snug text-[#1B2F8F] sm:text-[13px]">
                    Punya pertanyaan atau membutuhkan solusi perlindungan yang tepat? Tim Vision siap membantu Anda memahami kebutuhan, memberikan rekomendasi, serta mendampingi setiap proses dengan layanan yang personal dan terpercaya.
                </p>
            </div>

        </div>
    </div>
</section>