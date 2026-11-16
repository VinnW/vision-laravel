{{--
    Dashboard Agen (halaman setelah login)
    Lokasi: resources/views/agent/agent_dashboard.blade.php  (sesuai struktur folder kamu)

    Ini halaman penuh (bukan section), sejajar dengan admin/admin_dashboard.blade.php.

    VARIABEL YANG DITERIMA:
      $agent : object { username, name, bio, image_url, wa_link }  (agen yang sedang login)

    Route yang dipakai (opsional, ada fallback URL bila belum didaftarkan):
      agent.edit    → halaman edit profil
      agent.logout  → POST logout agen
--}}

@php
    $username = (string) ($agent->username ?? '');
    $name     = trim((string) ($agent->name ?? ''));
    $shareUrl = url('/' . $username);
    $editUrl  = Route::has('agent.edit') ? route('agent.edit') : url('/agent/edit');
    $logout   = Route::has('agent.logout') ? route('agent.logout') : url('/agent/logout');

    // Kelengkapan profil
    $checks = [
        ['label' => 'Foto profil',          'ok' => trim((string) ($agent->image_url ?? '')) !== ''],
        ['label' => 'Nama',                 'ok' => $name !== ''],
        ['label' => 'Pengalaman/deskripsi', 'ok' => trim((string) ($agent->bio ?? '')) !== ''],
        ['label' => 'Link WhatsApp',        'ok' => trim((string) ($agent->wa_link ?? '')) !== ''],
    ];

    $done      = count(array_filter($checks, fn ($c) => $c['ok']));
    $percent   = (int) round($done / count($checks) * 100);

    // Halaman publik terbit bila nama & WhatsApp sudah terisi
    $published = $checks[1]['ok'] && $checks[3]['ok'];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Agen | Vision</title>

    {{-- Sesuaikan dengan cara admin_dashboard memuat Tailwind + Alpine --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    {{-- ================= TOP BAR ================= --}}
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-6">
            <span class="text-base font-extrabold tracking-tight text-[#1F4E9E]">Vision Agent</span>

            <div class="flex items-center gap-4">
                <span class="hidden text-sm text-slate-500 sm:inline">
                    Masuk sebagai <span class="font-semibold text-slate-800">{{ $name !== '' ? $name : $username }}</span>
                </span>

                <form method="POST" action="{{ $logout }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>


    <main
        x-data="{
            copied: false,
            copy() {
                navigator.clipboard.writeText(@js($shareUrl)).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                });
            },
        }"
        class="mx-auto max-w-6xl px-6 py-10"
    >

        {{-- Sapaan --}}
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#10127A] sm:text-3xl">
                Halo, {{ $name !== '' ? $name : $username }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola halaman agen Anda dan bagikan linknya ke customer.
            </p>
        </div>

        <div class="mt-8 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)]">

            {{-- ============ KOLOM KIRI ============ --}}
            <div class="space-y-6">

                {{-- Status + link --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-900">Link halaman Anda</h2>

                        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $published ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $published ? 'Aktif' : 'Belum terbit' }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="min-w-0 flex-1 break-all text-sm font-medium text-[#1B2F8F]">{{ $shareUrl }}</span>
                        <button
                            type="button"
                            @click="copy()"
                            class="shrink-0 rounded-lg bg-[#1B3478] px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#10127A]"
                        >
                            <span x-show="!copied">Salin</span>
                            <span x-show="copied" x-cloak>Tersalin</span>
                        </button>
                    </div>

                    @unless ($published)
                        <p class="mt-3 text-xs leading-relaxed text-amber-700">
                            Customer belum bisa membuka link ini. Lengkapi nama dan link WhatsApp terlebih dahulu.
                        </p>
                    @else
                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            Bagikan link ini ke customer. Tombol edit hanya muncul untuk Anda saat login.
                        </p>
                    @endunless

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ $editUrl }}"
                           class="rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-7 py-2.5 text-xs font-medium text-white shadow-[0_10px_22px_-10px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5">
                            Edit profil
                        </a>

                        <a href="{{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                           class="rounded-full border-2 border-[#10127A] px-7 py-2 text-xs font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white">
                            Buka halaman
                        </a>
                    </div>
                </section>

                {{-- Kelengkapan profil --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6">
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-base font-semibold text-slate-900">Kelengkapan profil</h2>
                        <span class="text-sm font-bold text-[#1B3478]">{{ $percent }}%</span>
                    </div>

                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full rounded-full bg-linear-to-r from-[#6BB0FF] to-[#1B3478]" style="width: {{ $percent }}%"></div>
                    </div>

                    <ul class="mt-5 space-y-3">
                        @foreach ($checks as $c)
                            <li class="flex items-center gap-3 text-sm">
                                @if ($c['ok'])
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" /></svg>
                                    </span>
                                    <span class="text-slate-700">{{ $c['label'] }}</span>
                                @else
                                    <span class="h-5 w-5 shrink-0 rounded-full border-2 border-slate-300"></span>
                                    <span class="text-slate-500">{{ $c['label'] }}</span>
                                    <a href="{{ $editUrl }}" class="ml-auto text-xs font-semibold text-[#1B3478] hover:underline">Lengkapi</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>

            </div>


            {{-- ============ KOLOM KANAN: PRATINJAU ============ --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h2 class="text-sm font-semibold text-slate-900">Pratinjau halaman</h2>
                    <span class="truncate pl-3 text-xs text-slate-400">{{ $shareUrl }}</span>
                </div>

                {{-- Memuat halaman publik yang sama. Karena Anda sedang login, bar edit ikut terlihat di sini. --}}
                <iframe
                    src="{{ $shareUrl }}"
                    title="Pratinjau halaman agen"
                    loading="lazy"
                    class="h-[620px] w-full bg-white"
                ></iframe>
            </section>

        </div>
    </main>

</body>
</html>