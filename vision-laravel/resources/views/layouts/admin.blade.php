<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin Vision Asurance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 antialiased">

<div x-data="{ sidebar: false }" class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0"
        :class="sidebar && 'translate-x-0'"
    >
        <div class="flex h-16 items-center gap-3 border-b border-slate-100 px-6">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-linear-to-br from-[#F2A93B] to-[#E8823C] text-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3 4 6v6c0 4.4 3.4 8.4 8 9 4.6-.6 8-4.6 8-9V6l-8-3Z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-indigo-700">Vision Asurance</span>
        </div>

        @php
            $nav = [
                ['label' => 'Home Panel',  'panel' => 'home',  'active' => true],
                ['label' => 'Event Panel', 'panel' => 'event', 'active' => false],
                ['label' => 'User Panel',  'panel' => 'user',  'active' => false],
            ];
        @endphp

        <nav class="space-y-1 p-4">
            @foreach ($nav as $item)
                <button
                    type="button"
                    @click="$dispatch('switch-panel', '{{ $item['panel'] }}')"
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-indigo-700"
                    :class="panel === '{{ $item['panel'] }}' && 'bg-indigo-50 text-indigo-700'"
                >
                    {{ $item['label'] }}
                    @unless ($item['active'])
                        <span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-400">Soon</span>
                    @endunless
                </button>
            @endforeach
        </nav>

        <div class="absolute inset-x-0 bottom-0 border-t border-slate-100 p-4">
            <p class="mb-3 px-2 text-xs text-slate-400">
                Masuk sebagai <span class="font-semibold text-slate-600">{{ auth('admin')->user()->name }}</span>
            </p>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-red-50 hover:text-red-600">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="flex-1 lg:pl-64">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/80 px-6 backdrop-blur">
            <button @click="sidebar = !sidebar" class="lg:hidden" aria-label="Menu">
                <svg class="h-6 w-6 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-base font-bold text-slate-800">@yield('title', 'Dashboard')</h1>
        </header>

        <main class="p-6 lg:p-10">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>