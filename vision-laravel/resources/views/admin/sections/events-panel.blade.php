{{--
    Event Panel — pembungkus dua bagian halaman Event:
    - Coming Up Next  (menyusul)
    - Vision Update   (vision-update-panel.blade.php)
--}}

<div x-data="{ tab: 'vision' }">

    {{-- Tab --}}
    <div class="mb-8 flex flex-wrap gap-2 border-b border-slate-200">

        <button
            type="button"
            @click="tab = 'coming'"
            class="-mb-px flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-semibold transition"
            :class="tab === 'coming'
                ? 'border-indigo-600 text-indigo-700'
                : 'border-transparent text-slate-500 hover:text-indigo-700'"
        >
            Coming Up Next
            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-400">Soon</span>
        </button>

        <button
            type="button"
            @click="tab = 'vision'"
            class="-mb-px border-b-2 px-4 py-3 text-sm font-semibold transition"
            :class="tab === 'vision'
                ? 'border-indigo-600 text-indigo-700'
                : 'border-transparent text-slate-500 hover:text-indigo-700'"
        >
            Vision Update
        </button>

    </div>


    {{-- Coming Up Next --}}
    <div x-show="tab === 'coming'" x-cloak>
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
            <p class="text-sm font-medium text-slate-500">
                Panel Coming Up Next akan dibuat setelah Vision Update selesai.
            </p>
        </div>
    </div>


    {{-- Vision Update --}}
    <div x-show="tab === 'vision'" x-cloak>
        @include('admin.sections.vision-update-panel')
    </div>

</div>