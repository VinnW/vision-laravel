{{--
    Contact Section (partial)
    - Split 2 kolom: form konsultasi di kiri, deskripsi + ilustrasi di kanan
    - Konsisten dengan pola di event.blade.php: heading uppercase extrabold,
      paragraph text-[15px] font-medium, tombol pill border-2 dengan hover invert
    - Tailwind CSS. $person_image opsional dikirim dari controller; fallback
      disediakan agar section tetap tampil standalone.
    - Dipakai baik di homepage (index.blade.php) maupun di halaman
      standalone contact.blade.php via @include
--}}
@php
    $person_image ??= null;
@endphp

<section class="bg-white py-16 lg:py-24">
    <div class="container mx-auto px-4">
        <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">

            {{-- ================= KIRI: Judul + Form ================= --}}
            <div>
                <h1 class="text-4xl font-extrabold uppercase leading-tight text-slate-900 sm:text-5xl">
                    Konsultasikan<br>Bersama Kami
                </h1>

                {{-- Notifikasi sukses (aktif setelah form disambungkan ke controller) --}}
                @if (session('success'))
                    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[15px] font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ Route::has('contact.submit') ? route('contact.submit') : '#' }}"
                    method="POST"
                    class="mt-10 space-y-6"
                >
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold uppercase tracking-wide text-slate-700">Nama</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-[15px] font-medium text-slate-900 transition-colors focus:border-[#F2A93B] focus:outline-none focus:ring-2 focus:ring-[#F2A93B]/30"
                            placeholder="Masukkan nama lengkap"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold uppercase tracking-wide text-slate-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-[15px] font-medium text-slate-900 transition-colors focus:border-[#F2A93B] focus:outline-none focus:ring-2 focus:ring-[#F2A93B]/30"
                            placeholder="nama@email.com"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label for="city" class="mb-2 block text-sm font-semibold uppercase tracking-wide text-slate-700">Kota</label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            value="{{ old('city') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-[15px] font-medium text-slate-900 transition-colors focus:border-[#F2A93B] focus:outline-none focus:ring-2 focus:ring-[#F2A93B]/30"
                            placeholder="Kota domisili"
                        >
                        @error('city')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold uppercase tracking-wide text-slate-700">No Telepon</label>
                        <input
                            type="tel"
                            name="phone"
                            id="phone"
                            value="{{ old('phone') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-[15px] font-medium text-slate-900 transition-colors focus:border-[#F2A93B] focus:outline-none focus:ring-2 focus:ring-[#F2A93B]/30"
                            placeholder="08xxxxxxxxxx"
                        >
                        @error('phone')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pesan --}}
                    <div>
                        <label for="message" class="mb-2 block text-sm font-semibold uppercase tracking-wide text-slate-700">Pesan</label>
                        <textarea
                            name="message"
                            id="message"
                            rows="6"
                            class="w-full resize-none rounded-xl border border-slate-300 px-4 py-3 text-[15px] font-medium text-slate-900 transition-colors focus:border-[#F2A93B] focus:outline-none focus:ring-2 focus:ring-[#F2A93B]/30"
                            placeholder="Tulis pertanyaan atau kebutuhan konsultasi kamu di sini"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit — pola pill button sama seperti di event.blade.php --}}
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-full border-2 border-slate-900 px-10 py-4 text-sm font-semibold text-slate-900 transition-all duration-200 hover:bg-slate-900 hover:text-white sm:w-auto"
                    >
                        Kirim Pesan
                    </button>
                </form>
            </div>

            {{-- ================= KANAN: Deskripsi + Ilustrasi ================= --}}
            <div class="relative flex flex-col">
                <p class="text-[15px] font-medium leading-relaxed text-slate-600">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices
                    gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem
                    ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices
                    gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.
                </p>

                {{-- Ilustrasi orang.
                     $person_image bisa dikirim dari controller nanti,
                     mis. asset('images/contact/consultant.png') --}}
                <div class="relative mt-8 flex flex-1 items-end justify-center lg:justify-end">
                    @if ($person_image)
                        <img
                            src="{{ $person_image }}"
                            alt="Ilustrasi konsultan"
                            class="max-h-[420px] w-auto object-contain object-bottom lg:max-h-[520px]"
                        >
                    @else
                        <img
                            src="{{ asset('images/placeholder-person.png') }}"
                            alt="Ilustrasi konsultan"
                            class="max-h-[420px] w-auto object-contain object-bottom lg:max-h-[520px]"
                            onerror="this.style.display='none'"
                        >
                    @endif
                </div>
            </div>
        </div>

        {{-- Teks kecil bawah --}}
        <div class="mt-16 space-y-1 text-xs font-medium leading-relaxed text-slate-400">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.</p>
        </div>
    </div>
</section>