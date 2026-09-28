{{--
    Vision Update Panel — kelola daftar Vision Update (gambar, judul, deskripsi)
    Berdiri sendiri supaya gampang di-debug:
    semua state, request ke API, dan markup panel ada di satu file.

    Endpoint (prefix /api/visionasurance):
    GET    /vision-update                                  -> daftar update
    POST   /create-vision-update                           -> tambah update
    POST   /update-vision-update-image/{id}/{image_type}   -> ganti gambar
    PUT    /update-vision-update-text/{id}                 -> ubah judul & deskripsi
    DELETE /delete-vision-update/{id}                      -> hapus update
--}}

<div x-data="visionUpdatePanel()" x-init="load()">

    {{-- Header --}}
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">

        <div>
            <h2 class="text-2xl font-extrabold text-indigo-700">
                Vision Update
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kelola update event yang telah diadakan. Format JPG / PNG / WEBP, maksimal 20 MB per gambar.
            </p>
        </div>

        <button
            type="button"
            @click="openCreate()"
            :disabled="busy || creating"
            class="rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-60"
        >
            + Tambah Update
        </button>

    </div>


    {{-- Notifikasi --}}
    <div
        x-show="flash.msg"
        x-cloak
        x-transition
        class="mb-6 rounded-xl px-4 py-3 text-sm"
        :class="
            flash.ok
                ? 'border border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border border-red-200 bg-red-50 text-red-700'
        "
        x-text="flash.msg"
    ></div>


    {{-- Form Tambah Update --}}
    <div
        x-show="creating"
        x-cloak
        x-transition
        class="mb-8 overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm"
    >

        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-extrabold text-indigo-700">Update Baru</h3>
        </div>

        <div class="grid gap-6 p-6 md:grid-cols-2">

            {{-- Gambar --}}
            <div>

                <div class="relative aspect-video overflow-hidden rounded-xl bg-slate-100">

                    <img
                        x-show="draft.preview"
                        :src="draft.preview"
                        alt=""
                        class="h-full w-full object-cover"
                    >

                    <div
                        x-show="!draft.preview"
                        class="flex h-full w-full flex-col items-center justify-center text-center"
                    >
                        <svg
                            class="mb-3 h-10 w-10 text-slate-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="3" y="4" width="18" height="14" rx="2"/>
                            <circle cx="8.5" cy="9.5" r="1.5"/>
                            <path d="m21 16-5-5-9 9"/>
                        </svg>

                        <p class="text-sm font-medium text-slate-500">Belum ada gambar.</p>
                    </div>

                </div>

                <div class="mt-3 flex items-center gap-2">

                    <label
                        class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                        :class="busy && 'pointer-events-none opacity-60'"
                    >
                        <span x-text="draft.preview ? 'Ganti Gambar' : 'Pilih Gambar'"></span>

                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="chooseDraft($event)"
                        >
                    </label>

                    <button
                        type="button"
                        x-show="draft.preview"
                        x-cloak
                        @click="clearDraftImage()"
                        :disabled="busy"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-60"
                    >
                        Batal
                    </button>

                </div>

            </div>


            {{-- Teks --}}
            <div>

                <div class="mb-4">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Judul
                    </label>

                    <input
                        type="text"
                        x-model="draft.title"
                        :disabled="busy"
                        maxlength="150"
                        placeholder="Judul update"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                    >
                </div>

                <div class="mb-5">
                    <div class="mb-2 flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Deskripsi
                        </label>

                        <span
                            class="text-[11px] text-slate-400"
                            x-text="draft.body.length + ' karakter'"
                        ></span>
                    </div>

                    <textarea
                        x-model="draft.body"
                        :disabled="busy"
                        rows="6"
                        placeholder="Tulis deskripsi update di sini…"
                        class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm leading-relaxed text-slate-700 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                    ></textarea>
                </div>

                <div class="flex flex-wrap items-center gap-3">

                    <button
                        type="button"
                        @click="create()"
                        :disabled="busy"
                        class="rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-60"
                    >
                        <span x-text="busy ? 'Memproses…' : 'Simpan Update'"></span>
                    </button>

                    <button
                        type="button"
                        @click="cancelCreate()"
                        :disabled="busy"
                        class="rounded-xl border border-slate-200 px-5 py-3 text-xs font-semibold text-slate-500 transition hover:bg-slate-50 disabled:opacity-60"
                    >
                        Batal
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- Skeleton --}}
    <div
        x-show="loading"
        x-cloak
        class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
    >
        <template x-for="i in 3" :key="i">
            <div class="h-96 animate-pulse rounded-2xl bg-slate-200/70"></div>
        </template>
    </div>


    {{-- Empty State --}}
    <div
        x-show="!loading && items.length === 0 && !creating"
        x-cloak
        class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center"
    >

        <svg
            class="mx-auto mb-4 h-10 w-10 text-slate-300"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect x="3" y="4" width="18" height="14" rx="2"/>
            <circle cx="8.5" cy="9.5" r="1.5"/>
            <path d="m21 16-5-5-9 9"/>
        </svg>

        <p class="text-sm font-medium text-slate-500">
            Belum ada Vision Update.
        </p>

        <p class="mt-1 text-xs text-slate-400">
            Klik “Tambah Update” untuk membuat update pertama.
        </p>

    </div>


    {{-- Grid --}}
    <div
        x-show="!loading"
        x-cloak
        class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
    >

        <template x-for="u in items" :key="u.id">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg">

                <div class="relative aspect-video bg-slate-100">

                    <img
                        :src="src(u.image_url)"
                        alt=""
                        loading="lazy"
                        class="h-full w-full object-cover"
                        @@error="$el.style.visibility = 'hidden'"
                        @@load="$el.style.visibility = 'visible'"
                    >

                    <span
                        class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-slate-600 backdrop-blur"
                        x-text="'Update #' + u.id"
                    ></span>

                </div>


                <div class="flex items-center gap-2 px-4 pt-4">

                    <label
                        class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                        :class="busy && 'pointer-events-none opacity-60'"
                    >
                        Ganti Gambar

                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="replaceImage(u, $event)"
                        >
                    </label>

                    <button
                        type="button"
                        @click="destroy(u)"
                        :disabled="busy"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-60"
                    >
                        Hapus
                    </button>

                </div>


                <div class="p-4">

                    <div class="mb-4">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Judul
                        </label>

                        <input
                            type="text"
                            x-model="u.form.title"
                            :disabled="busy"
                            maxlength="150"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                        >
                    </div>

                    <div class="mb-4">
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Deskripsi
                            </label>

                            <span
                                class="text-[11px] text-slate-400"
                                x-text="u.form.body.length + ' karakter'"
                            ></span>
                        </div>

                        <textarea
                            x-model="u.form.body"
                            :disabled="busy"
                            rows="5"
                            class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm leading-relaxed text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                        ></textarea>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">

                        <button
                            type="button"
                            @click="saveText(u)"
                            :disabled="busy || !dirty(u)"
                            class="rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-5 py-2.5 text-xs font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-50"
                        >
                            Simpan Perubahan
                        </button>

                        <button
                            type="button"
                            x-show="dirty(u)"
                            x-cloak
                            @click="resetItem(u)"
                            :disabled="busy"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-700 disabled:opacity-60"
                        >
                            Kembalikan
                        </button>

                    </div>

                </div>

            </div>

        </template>

    </div>

</div>


<script>
function visionUpdatePanel() {

    const base = '{{ url('/api/visionasurance') }}';

    const csrfElement = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfElement ? csrfElement.content : '';

    const MAX = 20 * 1024 * 1024;
    const TAG = '[VisionUpdatePanel]';

    // Nilai image_type untuk update baru (kolom wajib di controller).
    // Ubah di sini jika database memakai nilai lain.
    const IMAGE_TYPE = 'main';

    const blankDraft = () => ({
        title: '',
        body: '',
        file: null,
        preview: ''
    });

    return {

        items: [],
        loading: true,
        busy: false,

        creating: false,
        draft: blankDraft(),

        flash: {
            ok: true,
            msg: ''
        },


        src(path) {

            const p = path || '';

            if (!p) {
                return '';
            }

            if (/^https?:\/\//.test(p)) {
                return p;
            }

            if (p.startsWith('/')) {
                return p;
            }

            if (
                p.startsWith('storage/') ||
                p.startsWith('images/') ||
                p.startsWith('uploads/')
            ) {
                return `{{ url('/') }}/${p}`;
            }

            return `{{ url('/storage') }}/${p}`;
        },


        notify(ok, msg) {

            this.flash = {
                ok: ok,
                msg: msg
            };

            clearTimeout(this._t);

            this._t = setTimeout(() => {
                this.flash.msg = '';
            }, 4000);
        },


        pick(event) {

            const file = event.target.files[0];

            event.target.value = '';

            if (!file) {
                return null;
            }

            if (!file.type.startsWith('image/')) {

                this.notify(
                    false,
                    'File harus berupa gambar.'
                );

                return null;
            }

            if (file.size > MAX) {

                this.notify(
                    false,
                    'Ukuran gambar maksimal 20 MB.'
                );

                return null;
            }

            return file;
        },


        dirty(u) {

            return (
                u.form.title !== u.original.title ||
                u.form.body !== u.original.body
            );
        },


        resetItem(u) {

            u.form.title = u.original.title;
            u.form.body = u.original.body;
        },


        /* ---------- Form tambah ---------- */

        openCreate() {

            this.creating = true;
        },


        cancelCreate() {

            this.clearDraftImage();

            this.draft = blankDraft();
            this.creating = false;
        },


        chooseDraft(event) {

            const file = this.pick(event);

            if (!file) {
                return;
            }

            this.clearDraftImage();

            this.draft.file = file;
            this.draft.preview = URL.createObjectURL(file);
        },


        clearDraftImage() {

            if (this.draft.preview) {
                URL.revokeObjectURL(this.draft.preview);
            }

            this.draft.file = null;
            this.draft.preview = '';
        },


        /* ---------- Request ---------- */

        async call(url, options = {}) {

            console.log(
                TAG,
                options.method || 'GET',
                url
            );

            const res = await fetch(url, {
                ...options,

                credentials: 'same-origin',

                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',

                    ...(options.headers || {})
                }
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {

                console.error(
                    TAG,
                    'gagal:',
                    data
                );

                throw new Error(
                    typeof data === 'string'
                        ? data
                        : (data.message || 'Terjadi kesalahan.')
                );
            }

            return data;
        },


        async load() {

            this.loading = true;

            try {

                const data = await this.call(
                    `${base}/vision-update`
                );

                let rows = Array.isArray(data)
                    ? data
                    : [];

                // Jaga-jaga bila respons masih terbungkus array tambahan: [[...]]
                if (rows.length === 1 && Array.isArray(rows[0])) {
                    rows = rows[0];
                }

                const previous = new Map(
                    this.items.map((u) => [u.id, u])
                );

                this.items = rows.map((r) => {

                    const original = {
                        title: r.text_title || '',
                        body: r.text_body || ''
                    };

                    // Pertahankan ketikan yang belum disimpan pada kartu lain
                    const prev = previous.get(r.id);
                    const keep = prev && this.dirty(prev);

                    return {
                        id: r.id,
                        image_type: r.image_type,
                        image_url: r.image_url || '',
                        original: original,
                        form: keep
                            ? { ...prev.form }
                            : { ...original }
                    };
                });

            } catch (e) {

                console.error(TAG, e);

                this.notify(
                    false,
                    e.message
                );

            } finally {

                this.loading = false;
            }
        },


        async create() {

            const title = this.draft.title.trim();
            const body = this.draft.body.trim();

            if (!this.draft.file) {

                this.notify(
                    false,
                    'Gambar harus diunggah!'
                );

                return;
            }

            if (!title) {

                this.notify(
                    false,
                    'Judul harus diisi!'
                );

                return;
            }

            if (!body) {

                this.notify(
                    false,
                    'Deskripsi harus diisi!'
                );

                return;
            }

            const form = new FormData();

            form.append('image_url', this.draft.file);
            form.append('image_type', IMAGE_TYPE);
            form.append('text_title', title);
            form.append('text_body', body);

            this.busy = true;

            try {

                const data = await this.call(
                    `${base}/create-vision-update`,
                    {
                        method: 'POST',
                        body: form
                    }
                );

                this.notify(
                    true,
                    data.message || 'Update berhasil dibuat!'
                );

                this.cancelCreate();

                await this.load();

            } catch (e) {

                this.notify(
                    false,
                    e.message
                );

            } finally {

                this.busy = false;
            }
        },


        async replaceImage(u, event) {

            const file = this.pick(event);

            if (!file) {
                return;
            }

            const form = new FormData();

            form.append('image_url', file);

            this.busy = true;

            try {

                const data = await this.call(
                    `${base}/update-vision-update-image/${u.id}/${encodeURIComponent(u.image_type)}`,
                    {
                        method: 'POST',
                        body: form
                    }
                );

                this.notify(
                    true,
                    data.message || 'Gambar berhasil diperbarui!'
                );

                await this.load();

            } catch (e) {

                this.notify(
                    false,
                    e.message
                );

            } finally {

                this.busy = false;
            }
        },


        async saveText(u) {

            const title = u.form.title.trim();
            const body = u.form.body.trim();

            if (!title) {

                this.notify(
                    false,
                    'Judul tidak boleh kosong!'
                );

                return;
            }

            if (!body) {

                this.notify(
                    false,
                    'Deskripsi tidak boleh kosong!'
                );

                return;
            }

            this.busy = true;

            try {

                // Route memakai PUT, jadi dikirim sebagai JSON
                const data = await this.call(
                    `${base}/update-vision-update-text/${u.id}`,
                    {
                        method: 'PUT',

                        headers: {
                            'Content-Type': 'application/json'
                        },

                        body: JSON.stringify({
                            text_title: title,
                            text_body: body
                        })
                    }
                );

                this.notify(
                    true,
                    data.message || 'Konten berhasil diperbarui!'
                );

                await this.load();

            } catch (e) {

                this.notify(
                    false,
                    e.message
                );

            } finally {

                this.busy = false;
            }
        },


        async destroy(u) {

            if (
                !confirm(
                    'Hapus update ini? Tindakan ini tidak bisa dibatalkan.'
                )
            ) {
                return;
            }

            this.busy = true;

            try {

                const data = await this.call(
                    `${base}/delete-vision-update/${u.id}`,
                    {
                        method: 'DELETE'
                    }
                );

                this.notify(
                    true,
                    data.message || 'Update berhasil dihapus!'
                );

                await this.load();

            } catch (e) {

                this.notify(
                    false,
                    e.message
                );

            } finally {

                this.busy = false;
            }
        }

    };
}
</script>