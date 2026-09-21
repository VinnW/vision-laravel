{{--
    Home Panel — kelola banner hero section
    Berdiri sendiri supaya gampang di-debug: semua state, request ke API,
    dan markup panel ini ada di satu file.
--}}
<div x-data="bannerPanel()" x-init="load()">

    {{-- Header --}}
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-indigo-700">Banner Halaman Utama</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kelola gambar hero section. Format JPG / PNG / WEBP, maksimal 4 MB.
            </p>
        </div>

        <label class="cursor-pointer rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition hover:-translate-y-0.5"
               :class="busy && 'pointer-events-none opacity-60'">
            <span x-text="busy ? 'Memproses…' : '+ Tambah Banner'"></span>
            <input type="file" accept="image/*" class="hidden" @change="create($event)">
        </label>
    </div>

    {{-- Notifikasi --}}
    <div x-show="flash.msg" x-cloak x-transition
         class="mb-6 rounded-xl px-4 py-3 text-sm"
         :class="flash.ok ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                          : 'border border-red-200 bg-red-50 text-red-700'"
         x-text="flash.msg"></div>

    {{-- Skeleton --}}
    <div x-show="loading" x-cloak class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="i in 3" :key="i">
            <div class="h-64 animate-pulse rounded-2xl bg-slate-200/70"></div>
        </template>
    </div>

    {{-- Empty state --}}
    <div x-show="!loading && banners.length === 0" x-cloak
         class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
        <svg class="mx-auto mb-4 h-10 w-10 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="14" rx="2"/>
            <circle cx="8.5" cy="9.5" r="1.5"/>
            <path d="m21 16-5-5-9 9"/>
        </svg>
        <p class="text-sm font-medium text-slate-500">Belum ada banner.</p>
        <p class="mt-1 text-xs text-slate-400">Klik “Tambah Banner” untuk mengunggah gambar pertama.</p>
    </div>

    {{-- Grid --}}
    <div x-show="!loading" x-cloak class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="(b, i) in banners" :key="b.id">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg">

                <div class="relative aspect-video bg-slate-100">
                    <img :src="src(b)" alt="" loading="lazy"
                         class="h-full w-full object-cover"
                         @error="$el.style.visibility = 'hidden'">
                    <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-slate-600 backdrop-blur"
                          x-text="'Slide ' + (i + 1)"></span>
                </div>

                <div class="flex items-center gap-2 p-4">
                    <label class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                           :class="busy && 'pointer-events-none opacity-60'">
                        Ganti Gambar
                        <input type="file" accept="image/*" class="hidden" @change="update(b.id, $event)">
                    </label>

                    <button type="button" @click="destroy(b.id)" :disabled="busy"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-60">
                        Hapus
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
    // console.log tag biar gampang ketauan panel mana yang lagi jalan saat debug
    function bannerPanel() {
        const base = '{{ url('visionasurance') }}';
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const MAX  = 4 * 1024 * 1024;
        const TAG  = '[HomePanel]';

        return {
            banners: [], loading: true, busy: false,
            flash: { ok: true, msg: '' },

            src(b) {
                const p = b.banner_url || '';
                if (/^https?:\/\//.test(p)) return p;
                if (p.startsWith('/'))      return p;
                if (p.startsWith('storage/') || p.startsWith('images/') || p.startsWith('uploads/')) {
                    return `{{ url('/') }}/${p}`;
                }
                return `{{ url('/storage') }}/${p}`;
            },

            notify(ok, msg) {
                this.flash = { ok, msg };
                clearTimeout(this._t);
                this._t = setTimeout(() => this.flash.msg = '', 4000);
            },

            pick(event) {
                const file = event.target.files[0];
                event.target.value = '';
                if (!file) return null;
                if (!file.type.startsWith('image/')) { this.notify(false, 'File harus berupa gambar.'); return null; }
                if (file.size > MAX)                 { this.notify(false, 'Ukuran gambar maksimal 4 MB.'); return null; }
                return file;
            },

            async call(url, options = {}) {
                console.log(TAG, options.method || 'GET', url);
                const res = await fetch(url, {
                    ...options,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(options.headers || {}),
                    },
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    console.error(TAG, 'gagal:', data);
                    throw new Error(typeof data === 'string' ? data : (data.message || 'Terjadi kesalahan.'));
                }
                return data;
            },

            async load() {
                this.loading = true;
                try {
                    const data = await this.call(`${base}/banners`);
                    this.banners = Array.isArray(data) ? data : [];
                } catch (e) {
                    this.notify(false, e.message);
                } finally {
                    this.loading = false;
                }
            },

            async create(event) {
                const file = this.pick(event);
                if (!file) return;

                const body = new FormData();
                body.append('banner_url', file);

                this.busy = true;
                try {
                    const data = await this.call(`${base}/create-banner`, { method: 'POST', body });
                    this.notify(true, data.message || 'Gambar berhasil diunggah!');
                    await this.load();
                } catch (e) {
                    this.notify(false, e.message);
                } finally {
                    this.busy = false;
                }
            },

            async update(id, event) {
                const file = this.pick(event);
                if (!file) return;

                const body = new FormData();
                body.append('banner_url', file);
                body.append('_method', 'PUT'); // spoofing: PHP tak parse file di request PUT asli

                this.busy = true;
                try {
                    const data = await this.call(`${base}/update-banner/${id}`, { method: 'POST', body });
                    this.notify(true, data.message || 'Gambar berhasil diperbarui!');
                    await this.load();
                } catch (e) {
                    this.notify(false, e.message);
                } finally {
                    this.busy = false;
                }
            },

            async destroy(id) {
                if (!confirm('Hapus banner ini? Tindakan ini tidak bisa dibatalkan.')) return;

                this.busy = true;
                try {
                    const data = await this.call(`${base}/delete-banner/${id}`, { method: 'DELETE' });
                    this.notify(true, data.message || 'Banner berhasil dihapus!');
                    await this.load();
                } catch (e) {
                    this.notify(false, e.message);
                } finally {
                    this.busy = false;
                }
            },
        };
    }
</script>
@endpush