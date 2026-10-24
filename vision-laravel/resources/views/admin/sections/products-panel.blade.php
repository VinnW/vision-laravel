{{--
    Admin — Products Panel
    Lokasi: resources/views/admin/sections/products-panel.blade.php

    Fitur:
    - Daftar semua produk (gambar, judul, ringkasan, status katalog PDF)
    - Tambah produk baru
    - Edit semua isi: banner kiri, banner atas, judul, deskripsi, katalog PDF
    - Ganti / hapus katalog PDF kapan saja
    - Hapus produk

    Endpoint (routes/api.php, prefix 'visionasurance' → URL asli diawali /api):
      GET  /api/visionasurance/products          → ProductsController@getProducts
      POST /api/visionasurance/create-products   → ProductsController@createProducts
      POST /api/visionasurance/update-products   → ProductsController@editProducts
      POST /api/visionasurance/delete-products   → ProductsController@deleteProducts

    Opsional: kirim $apiBase dari luar bila prefix route Anda berbeda.
--}}
@php
    $apiBase ??= url('/api/visionasurance');
@endphp

<section
    x-data="{
        apiBase: @js($apiBase),
        csrf: @js(csrf_token()),

        products: [],
        loading: true,
        loadError: '',

        modal: false,
        mode: 'create',
        saving: false,
        formError: '',
        form: { id: null, text_title: '', text_desc: '', catalog_name: '', remove_catalog: false },
        files: { left: null, top: null, catalog: null },
        previews: { left: '', top: '' },

        confirmTarget: null,
        deleting: false,

        toast: { show: false, type: 'ok', text: '' },
        toastTimer: null,

        // Path dari database → URL gambar
        src(path) {
            const p = (path || '').trim();
            if (!p) return '';
            if (/^https?:\/\//.test(p) || p.startsWith('/')) return p;
            if (p.startsWith('storage/') || p.startsWith('images/') || p.startsWith('uploads/')) return '/' + p;
            return '/storage/' + p;
        },

        notify(type, text) {
            this.toast = { show: true, type: type, text: text };
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        },

        // Baca respons API; lempar Error berisi pesan yang ramah bila gagal
        async parse(res) {
            let data = {};
            try { data = await res.json(); } catch (e) {}

            if (!res.ok) {
                let msg = data.message || ('Terjadi kesalahan (' + res.status + ')');
                if (data.errors) {
                    const first = Object.values(data.errors)[0];
                    if (first && first[0]) msg = first[0];
                }
                throw new Error(msg);
            }
            return data;
        },

        async load() {
            this.loading = true;
            this.loadError = '';
            try {
                const res = await fetch(this.apiBase + '/products', { headers: { 'Accept': 'application/json' } });
                this.products = await this.parse(res);
            } catch (e) {
                this.loadError = e.message;
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.form = { id: null, text_title: '', text_desc: '', catalog_name: '', remove_catalog: false };
            this.files = { left: null, top: null, catalog: null };
            this.previews = { left: '', top: '' };
            this.formError = '';
        },

        openCreate() {
            this.resetForm();
            this.mode = 'create';
            this.modal = true;
        },

        openEdit(p) {
            this.resetForm();
            this.mode = 'edit';
            this.form = {
                id: p.id,
                text_title: p.text_title || '',
                text_desc: p.text_desc || '',
                catalog_name: p.catalog_url ? (p.catalog_name || 'katalog.pdf') : '',
                remove_catalog: false,
            };
            this.previews = { left: this.src(p.image_url_left), top: this.src(p.image_url_top) };
            this.modal = true;
        },

        closeModal() {
            if (this.saving) return;
            this.modal = false;
        },

        // kind: 'left' | 'top' | 'catalog'
        pick(kind, event) {
            const file = event.target.files[0] || null;
            this.files[kind] = file;

            if (kind === 'catalog') {
                if (file) this.form.remove_catalog = false;
                return;
            }
            if (file) this.previews[kind] = URL.createObjectURL(file);
        },

        async submit() {
            this.formError = '';

            if (!this.form.text_title.trim()) { this.formError = 'Judul wajib diisi.'; return; }
            if (!this.form.text_desc.trim()) { this.formError = 'Deskripsi wajib diisi.'; return; }
            if (this.mode === 'create' && !this.files.left) { this.formError = 'Banner kiri wajib diunggah.'; return; }
            if (this.mode === 'create' && !this.files.top) { this.formError = 'Banner atas wajib diunggah.'; return; }

            const fd = new FormData();
            fd.append('text_title', this.form.text_title.trim());
            fd.append('text_desc', this.form.text_desc);
            if (this.mode === 'edit') fd.append('id', this.form.id);
            if (this.files.left) fd.append('image_url_left', this.files.left);
            if (this.files.top) fd.append('image_url_top', this.files.top);
            if (this.files.catalog) fd.append('catalog_file', this.files.catalog);
            else if (this.form.remove_catalog) fd.append('remove_catalog', '1');

            this.saving = true;
            try {
                const url = this.apiBase + (this.mode === 'edit' ? '/update-products' : '/create-products');
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await this.parse(res);

                this.modal = false;
                this.notify('ok', data.message || 'Berhasil disimpan.');
                await this.load();
            } catch (e) {
                this.formError = e.message;
            } finally {
                this.saving = false;
            }
        },

        async destroy() {
            if (!this.confirmTarget) return;

            const fd = new FormData();
            fd.append('id', this.confirmTarget.id);

            this.deleting = true;
            try {
                const res = await fetch(this.apiBase + '/delete-products', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await this.parse(res);

                this.confirmTarget = null;
                this.notify('ok', data.message || 'Produk dihapus.');
                await this.load();
            } catch (e) {
                this.notify('err', e.message);
            } finally {
                this.deleting = false;
            }
        },
    }"
    x-init="load()"
    @keydown.escape.window="closeModal(); confirmTarget = null"
    class="relative w-full"
>

    {{-- ===================== Header panel ===================== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-[#1F4E9E] sm:text-3xl">Products Panel</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">
                Kelola produk layanan yang tampil di beranda dan halaman detail.
            </p>
        </div>

        <button
            type="button"
            @click="openCreate()"
            class="inline-flex items-center gap-2 rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            Tambah Produk
        </button>
    </div>


    {{-- ===================== Daftar produk ===================== --}}
    <div class="mt-8">

        {{-- Loading --}}
        <template x-if="loading">
            <p class="rounded-2xl border border-slate-200 bg-white py-12 text-center text-sm font-medium text-slate-500">Memuat data produk...</p>
        </template>

        {{-- Error --}}
        <template x-if="!loading && loadError">
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                <span x-text="loadError"></span>
                <button type="button" @click="load()" class="ml-2 font-bold underline">Coba lagi</button>
            </div>
        </template>

        {{-- Kosong --}}
        <template x-if="!loading && !loadError && products.length === 0">
            <div class="rounded-[2rem] border-2 border-dashed border-[#6BB0FF]/60 bg-white py-16 text-center">
                <p class="text-sm font-medium text-slate-500">Belum ada produk. Klik "Tambah Produk" untuk membuat yang pertama.</p>
            </div>
        </template>

        {{-- List --}}
        <div class="grid gap-5 md:grid-cols-2" x-show="!loading && !loadError && products.length > 0">
            <template x-for="p in products" :key="p.id">
                <article class="flex gap-4 rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="h-32 w-24 shrink-0 overflow-hidden rounded-xl border-2 border-[#6BB0FF] bg-slate-100">
                        <img
                            x-show="p.image_url_left"
                            :src="src(p.image_url_left)"
                            :alt="p.text_title"
                            class="h-full w-full object-cover"
                            onerror="this.style.visibility='hidden'"
                        >
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col">
                        <h3 class="truncate text-base font-extrabold uppercase text-[#1F4E9E]" x-text="p.text_title"></h3>

                        <p class="mt-1 line-clamp-2 text-xs font-medium leading-snug text-slate-600" x-text="p.text_desc"></p>

                        <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold" :class="p.catalog_url ? 'text-emerald-700' : 'text-slate-400'">
                            <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" />
                                <path d="M14 3v5h5" />
                            </svg>
                            <span class="truncate" x-text="p.catalog_url ? (p.catalog_name || 'Katalog PDF') : 'Belum ada katalog PDF'"></span>
                        </p>

                        <div class="mt-auto flex items-center gap-2 pt-3">
                            <button
                                type="button"
                                @click="openEdit(p)"
                                class="rounded-full border-2 border-[#10127A] px-4 py-1 text-xs font-semibold text-[#10127A] transition-colors hover:bg-[#10127A] hover:text-white"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="confirmTarget = p"
                                class="rounded-full border-2 border-red-500 px-4 py-1 text-xs font-semibold text-red-600 transition-colors hover:bg-red-500 hover:text-white"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                </article>
            </template>
        </div>
    </div>


    {{-- ===================== Modal tambah / edit ===================== --}}
    <template x-if="modal">
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm sm:p-8" @click.self="closeModal()">

            <div class="relative w-full max-w-3xl rounded-[2rem] bg-white p-6 shadow-2xl sm:p-8">

                <button
                    type="button"
                    @click="closeModal()"
                    aria-label="Tutup"
                    class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18" /></svg>
                </button>

                <h3 class="text-xl font-extrabold text-[#1F4E9E]" x-text="mode === 'edit' ? 'Edit Produk' : 'Tambah Produk'"></h3>

                <form @submit.prevent="submit()" class="mt-6 space-y-5">

                    {{-- Judul --}}
                    <div>
                        <label for="pp-title" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul</label>
                        <input
                            id="pp-title"
                            type="text"
                            x-model="form.text_title"
                            maxlength="255"
                            placeholder="mis. Asuransi Perjalanan"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-900 focus:border-[#2F57A8] focus:outline-none focus:ring-2 focus:ring-[#2F57A8]/20"
                        >
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="pp-desc" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
                        <textarea
                            id="pp-desc"
                            rows="9"
                            x-model="form.text_desc"
                            placeholder="Tulis deskripsi lengkap produk. Gunakan baris kosong untuk memisahkan paragraf."
                            class="w-full resize-y rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium leading-relaxed text-slate-900 focus:border-[#2F57A8] focus:outline-none focus:ring-2 focus:ring-[#2F57A8]/20"
                        ></textarea>
                        <p class="mt-1 text-xs text-slate-400">Satu baris kosong = paragraf baru. Baris baru biasa tetap dipertahankan di halaman detail.</p>
                    </div>

                    {{-- Gambar --}}
                    <div class="grid gap-5 sm:grid-cols-2">

                        {{-- Banner kiri --}}
                        <div>
                            <span class="mb-1.5 block text-sm font-semibold text-slate-700">Banner Kiri (besar)</span>

                            <div class="flex h-44 items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-[#6BB0FF]/70 bg-slate-50">
                                <img x-show="previews.left" :src="previews.left" alt="Preview banner kiri" class="h-full w-full object-cover">
                                <span x-show="!previews.left" class="px-3 text-center text-xs font-medium text-slate-400">Belum ada gambar</span>
                            </div>

                            <input
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                @change="pick('left', $event)"
                                class="mt-2 block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-[#10127A] file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#1F4FD1]"
                            >
                            <p class="mt-1 text-xs text-slate-400" x-show="mode === 'edit'">Kosongkan bila tidak ingin mengganti.</p>
                        </div>

                        {{-- Banner atas --}}
                        <div>
                            <span class="mb-1.5 block text-sm font-semibold text-slate-700">Banner Atas (kecil)</span>

                            <div class="flex h-44 items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-[#6BB0FF]/70 bg-slate-50">
                                <img x-show="previews.top" :src="previews.top" alt="Preview banner atas" class="h-full w-full object-contain">
                                <span x-show="!previews.top" class="px-3 text-center text-xs font-medium text-slate-400">Belum ada gambar</span>
                            </div>

                            <input
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                @change="pick('top', $event)"
                                class="mt-2 block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-[#10127A] file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#1F4FD1]"
                            >
                            <p class="mt-1 text-xs text-slate-400" x-show="mode === 'edit'">Kosongkan bila tidak ingin mengganti.</p>
                        </div>
                    </div>

                    {{-- Katalog PDF --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Katalog PDF</span>

                        <p x-show="form.catalog_name && !form.remove_catalog && !files.catalog" class="mb-2 text-xs font-semibold text-emerald-700">
                            Katalog saat ini: <span x-text="form.catalog_name"></span>
                        </p>

                        <p x-show="form.remove_catalog" class="mb-2 text-xs font-semibold text-red-600">
                            Katalog akan dihapus saat disimpan.
                        </p>

                        <input
                            type="file"
                            accept="application/pdf"
                            @change="pick('catalog', $event)"
                            class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-[#10127A] file:px-4 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-[#1F4FD1]"
                        >
                        <p class="mt-1 text-xs text-slate-400">
                            Maksimal 20 MB. File baru otomatis menggantikan katalog lama.
                        </p>

                        <label x-show="mode === 'edit' && form.catalog_name && !files.catalog" class="mt-3 flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <input type="checkbox" x-model="form.remove_catalog" class="h-4 w-4 rounded border-slate-300">
                            Hapus katalog ini (tombol "Download PDF" akan disembunyikan)
                        </label>
                    </div>

                    {{-- Error --}}
                    <p x-show="formError" x-text="formError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700" role="alert"></p>

                    {{-- Aksi --}}
                    <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="saving"
                            class="rounded-full border-2 border-slate-300 px-6 py-2 text-sm font-semibold text-slate-600 transition-colors hover:bg-slate-100 disabled:opacity-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            :disabled="saving"
                            class="min-w-[8.5rem] rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-8 py-2 text-sm font-semibold text-white shadow-[0_12px_24px_-12px_rgba(27,52,120,0.75)] transition-all duration-200 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </template>


    {{-- ===================== Konfirmasi hapus ===================== --}}
    <template x-if="confirmTarget">
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 p-6 backdrop-blur-sm" @click.self="confirmTarget = null">
            <div class="w-full max-w-sm rounded-[2rem] bg-white p-7 text-center shadow-2xl">
                <h3 class="text-lg font-extrabold text-slate-900">Hapus produk ini?</h3>
                <p class="mt-2 text-sm font-medium text-slate-500">
                    "<span x-text="confirmTarget.text_title"></span>" beserta gambar dan katalog PDF-nya akan dihapus permanen.
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button
                        type="button"
                        @click="confirmTarget = null"
                        :disabled="deleting"
                        class="rounded-full border-2 border-slate-300 px-6 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 disabled:opacity-50"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        @click="destroy()"
                        :disabled="deleting"
                        class="rounded-full bg-red-600 px-6 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-60"
                    >
                        <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>


    {{-- ===================== Toast ===================== --}}
    <div
        x-show="toast.show"
        x-transition
        :class="toast.type === 'ok' ? 'bg-emerald-600' : 'bg-red-600'"
        class="fixed bottom-6 right-6 z-[70] max-w-sm rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-lg"
        style="display: none;"
        role="status"
    >
        <span x-text="toast.text"></span>
    </div>

</section>