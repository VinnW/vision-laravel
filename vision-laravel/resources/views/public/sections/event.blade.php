{{--
    Event Section
    - Split 2 kolom full-bleed (tanpa rounded corner), masing-masing punya
      background gambar sendiri (dengan overlay warna dark/light) dan tombol
      pill sendiri yang mengarah ke section/route lain.
    - Konsisten dengan pola di service.blade.php: kalau 'image' kosong,
      fallback ke warna solid (tanpa dependensi internet).
    - Tailwind CSS. $columns dikirim dari controller; fallback disediakan
      agar section tetap tampil standalone.
--}}
@php
    $columns ??= [
        [
            'tone'      => 'dark',
            'heading'   => "Coming Up\nNext",
            'caption'   => "Desain Image Visual\nEvent Mendatang",
            'paragraph' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
            'image'     => null,
            'action'    => ['label' => 'Daftar Sekarang', 'route' => 'event-register'],
        ],
        [
            'tone'      => 'light',
            'heading'   => "Vision\nUpdate",
            'caption'   => "Desain Image Visual\nEvent yang telah diadakan",
            'paragraph' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.',
            'image'     => null,
            'action'    => ['label' => 'Baca Selengkapnya', 'route' => 'event-recap'],
        ],
    ];
@endphp

<section class="grid grid-cols-1 lg:grid-cols-2">
    @foreach ($columns as $column)
        @php
            $isDark = ($column['tone'] ?? 'dark') === 'dark';
        @endphp

        <div
            @if(!empty($column['image']))
                style="background-image: linear-gradient({{ $isDark ? 'rgba(15,23,42,.68),rgba(15,23,42,.68)' : 'rgba(255,255,255,.8),rgba(255,255,255,.8)' }}), url('{{ $column['image'] }}'); background-size: cover; background-position: center;"
            @endif
            class="relative flex min-h-152 flex-col justify-between px-8 py-14 sm:px-12 lg:min-h-176 lg:px-16 lg:py-20 {{ $isDark ? 'bg-slate-500 text-white' : 'bg-slate-200 text-slate-900' }}"
        >
            {{-- Heading --}}
            <h2 class="text-4xl font-extrabold uppercase leading-tight sm:text-5xl">
                {!! nl2br(e($column['heading'])) !!}
            </h2>

            {{-- Icon + caption, centered --}}
            <div class="my-auto flex flex-col items-center gap-5 py-12 text-center">
                <svg
                    class="h-16 w-16 {{ $isDark ? 'text-white' : 'text-slate-700' }}"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"
                >
                    <rect x="3" y="3" width="18" height="14" rx="2" transform="translate(0 1)" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="m21 15-5-5-9 9" />
                </svg>
                <p class="text-lg font-bold uppercase leading-snug tracking-wide {{ $isDark ? 'text-white/90' : 'text-slate-700' }}">
                    {!! nl2br(e($column['caption'])) !!}
                </p>
            </div>

            {{-- Paragraph + CTA --}}
            <div class="max-w-md space-y-8">
                <p class="text-[15px] font-medium leading-relaxed {{ $isDark ? 'text-white/85' : 'text-slate-600' }}">
                    {{ $column['paragraph'] }}
                </p>

                <a
                    href="{{ Route::has($column['action']['route']) ? route($column['action']['route']) : '#' }}"
                    class="inline-flex w-full items-center justify-center rounded-full border-2 px-10 py-4 text-sm font-semibold transition-all duration-200 sm:w-auto
                        {{ $isDark
                            ? 'border-white text-white hover:bg-white hover:text-slate-900'
                            : 'border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white' }}"
                >
                    {{ $column['action']['label'] }}
                </a>
            </div>
        </div>
    @endforeach
</section>