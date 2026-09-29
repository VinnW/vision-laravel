<div x-data="comingPanel()" x-init="load()">

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-indigo-700">
            Coming Up Next
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Kelola event mendatang. Hanya ada satu konten yang terdiri dari
            gambar, judul, dan deskripsi. Format JPG / PNG / WEBP, maksimal 20 MB.
        </p>
    </div>


    {{-- Notification --}}
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


    {{-- Loading --}}
    <div
        x-show="loading"
        x-cloak
        class="h-96 animate-pulse rounded-2xl bg-slate-200/70"
    ></div>


    {{-- Main Card --}}
    <div
        x-show="!loading"
        x-cloak
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >

        {{-- Card Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">

            <h3 class="text-base font-extrabold text-indigo-700">
                <span
                    x-text="exists ? 'Konten Saat Ini' : 'Konten Baru'"
                ></span>
            </h3>

            <span
                class="rounded-full px-3 py-1 text-[11px] font-semibold"
                :class="
                    exists
                        ? 'bg-emerald-50 text-emerald-700'
                        : 'bg-slate-100 text-slate-500'
                "
                x-text="
                    exists
                        ? 'Aktif'
                        : 'Belum ada konten'
                "
            ></span>

        </div>


        {{-- Content --}}
        <div class="grid gap-6 p-6 md:grid-cols-2">


            {{-- ======================================================
                 IMAGE
            ======================================================= --}}
            <div>

                {{-- Image Preview --}}
                <div class="relative aspect-video overflow-hidden rounded-xl bg-slate-100">

                    <img
                        x-show="imageSrc()"
                        :src="imageSrc()"
                        alt=""
                        class="h-full w-full object-cover"
                        @@error="$el.style.visibility = 'hidden'"
                        @@load="$el.style.visibility = 'visible'"
                    >


                    {{-- Empty Image --}}
                    <div
                        x-show="!imageSrc()"
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
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="14"
                                rx="2"
                            />

                            <circle
                                cx="8.5"
                                cy="9.5"
                                r="1.5"
                            />

                            <path d="m21 16-5-5-9 9"/>
                        </svg>

                        <p class="text-sm font-medium text-slate-500">
                            Belum ada gambar.
                        </p>

                    </div>


                    {{-- Unsaved Preview --}}
                    <span
                        x-show="preview"
                        x-cloak
                        class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-amber-600 backdrop-blur"
                    >
                        Belum disimpan
                    </span>

                </div>


                {{-- Image Action --}}
                <div class="mt-3 flex items-center gap-2">

                    <label
                        class="flex-1 cursor-pointer rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                        :class="busy && 'pointer-events-none opacity-60'"
                    >

                        <span
                            x-text="
                                imageSrc()
                                    ? 'Ganti Gambar'
                                    : 'Pilih Gambar'
                            "
                        ></span>

                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="chooseImage($event)"
                        >

                    </label>


                    <button
                        type="button"
                        x-show="preview"
                        x-cloak
                        @click="clearImage()"
                        :disabled="busy"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-60"
                    >
                        Batal
                    </button>

                </div>

            </div>


            {{-- ======================================================
                 TEXT
            ======================================================= --}}
            <div>

                {{-- Title --}}
                <div class="mb-4">

                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Judul
                    </label>

                    <input
                        type="text"
                        x-model="form.title"
                        :disabled="busy"
                        maxlength="150"
                        placeholder="Judul event"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                    >

                </div>


                {{-- Description --}}
                <div class="mb-5">

                    <div class="mb-2 flex items-center justify-between">

                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Deskripsi
                        </label>

                        <span
                            class="text-[11px] text-slate-400"
                            x-text="
                                form.description.length + ' karakter'
                            "
                        ></span>

                    </div>


                    <textarea
                        x-model="form.description"
                        :disabled="busy"
                        rows="8"
                        placeholder="Tulis deskripsi event di sini..."
                        class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm leading-relaxed text-slate-700 outline-none transition placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 disabled:opacity-60"
                    ></textarea>


                    <p class="mt-2 text-[11px] text-slate-400">
                        Baris kosong akan menjadi paragraf baru di halaman publik.
                    </p>

                </div>


                {{-- Buttons --}}
                <div class="flex flex-wrap items-center gap-3">

                    {{-- Create / Update --}}
                    <button
                        type="button"
                        @click="save()"
                        :disabled="busy || !canSave()"
                        class="rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-6 py-3 text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-50"
                    >
                        <span
                            x-text="
                                busy
                                    ? 'Memproses...'
                                    : (
                                        exists
                                            ? 'Simpan Perubahan'
                                            : 'Buat Konten'
                                    )
                            "
                        ></span>
                    </button>


                    {{-- Reset --}}
                    <button
                        type="button"
                        x-show="exists && dirty()"
                        x-cloak
                        @click="reset()"
                        :disabled="busy"
                        class="rounded-xl border border-slate-200 px-5 py-3 text-xs font-semibold text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-700 disabled:opacity-60"
                    >
                        Kembalikan
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

function comingPanel() {

    const base = '{{ url('/api/visionasurance') }}';

    const csrfElement = document.querySelector(
        'meta[name="csrf-token"]'
    );

    const csrf = csrfElement
        ? csrfElement.content
        : '';

    const MAX = 20 * 1024 * 1024;

    const TAG = '[ComingPanel]';


    return {

        /* ==========================================================
           STATE
        =========================================================== */

        loading: true,

        busy: false,

        /*
         * false = database belum punya konten
         * true  = database sudah punya konten
         */
        exists: false,


        /* ==========================================================
           DATA DARI DATABASE
        =========================================================== */

        original: {
            title: '',
            description: ''
        },

        imageUrl: '',


        /* ==========================================================
           DATA FORM
           Ini bisa digunakan walaupun database masih kosong.
        =========================================================== */

        form: {
            title: '',
            description: ''
        },

        file: null,

        preview: '',


        /* ==========================================================
           FLASH
        =========================================================== */

        flash: {
            ok: true,
            msg: ''
        },


        /* ==========================================================
           IMAGE URL
        =========================================================== */

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


        imageSrc() {

            /*
             * Preview gambar baru memiliki prioritas.
             * Kalau tidak ada preview, gunakan gambar dari database.
             */

            return this.preview || this.src(this.imageUrl);

        },


        /* ==========================================================
           NOTIFICATION
        =========================================================== */

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


        /* ==========================================================
           DIRTY
        =========================================================== */

        dirty() {

            return (

                !!this.file ||

                this.form.title !== this.original.title ||

                this.form.description !== this.original.description

            );

        },


        /* ==========================================================
           CAN SAVE
        =========================================================== */

        canSave() {

            /*
             * Kalau belum ada konten:
             * panel boleh membuat konten jika user sudah
             * memasukkan minimal salah satu data.
             */

            if (!this.exists) {

                return (

                    !!this.file ||

                    this.form.title.trim() !== '' ||

                    this.form.description.trim() !== ''

                );

            }


            /*
             * Kalau sudah ada:
             * hanya aktif apabila terjadi perubahan.
             */

            return this.dirty();

        },


        /* ==========================================================
           RESET
        =========================================================== */

        reset() {

            this.clearImage();


            this.form.title =
                this.original.title;


            this.form.description =
                this.original.description;

        },


        /* ==========================================================
           CHOOSE IMAGE
        =========================================================== */

        chooseImage(event) {

            const file =
                event.target.files[0];


            /*
             * Reset input supaya file yang sama
             * bisa dipilih lagi jika diperlukan.
             */

            event.target.value = '';


            if (!file) {
                return;
            }


            if (!file.type.startsWith('image/')) {

                this.notify(
                    false,
                    'File harus berupa gambar.'
                );

                return;

            }


            if (file.size > MAX) {

                this.notify(
                    false,
                    'Ukuran gambar maksimal 20 MB.'
                );

                return;

            }


            this.clearImage();


            this.file = file;


            this.preview =
                URL.createObjectURL(file);

        },


        /* ==========================================================
           CLEAR IMAGE
        =========================================================== */

        clearImage() {

            if (this.preview) {

                URL.revokeObjectURL(
                    this.preview
                );

            }


            this.file = null;

            this.preview = '';

        },


        /* ==========================================================
           API CALL
        =========================================================== */

        async call(url, options = {}) {

            console.log(
                TAG,
                options.method || 'GET',
                url
            );


            const res = await fetch(

                url,

                {

                    ...options,

                    credentials: 'same-origin',

                    headers: {

                        'X-CSRF-TOKEN':
                            csrf,

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest',

                        ...(options.headers || {})

                    }

                }

            );


            const data =
                await res.json()
                    .catch(() => null);


            if (!res.ok) {

                console.error(
                    TAG,
                    'HTTP:',
                    res.status
                );


                console.error(
                    TAG,
                    'Response:',
                    data
                );


                throw new Error(

                    (data && data.message)

                    ||

                    `Terjadi kesalahan (${res.status}).`

                );

            }


            return data;

        },


        /* ==========================================================
           LOAD
        =========================================================== */

        async load() {

            this.loading = true;


            try {

                const data = await this.call(
                    `${base}/coming-up-next-content`
                );


                /*
                 * API menggunakan:
                 *
                 * DB::table(...)->first()
                 *
                 * Maka:
                 *
                 * tabel kosong
                 * -> null
                 *
                 * tabel memiliki konten
                 * -> object
                 *
                 * Kita tidak menggunakan data.id.
                 */

                const row = (

                    data &&

                    typeof data === 'object' &&

                    !Array.isArray(data)

                )
                    ? data
                    : null;


                /*
                 * TRUE jika API benar-benar mengembalikan object.
                 */

                this.exists = !!row;


                if (row) {

                    /* ----------------------------------------------
                       DATABASE SUDAH MEMILIKI KONTEN
                    ---------------------------------------------- */

                    this.original = {

                        title:
                            row.title || '',

                        description:
                            row.description || ''

                    };


                    this.imageUrl =
                        row.image_url || '';


                    /*
                     * Masukkan data database ke form.
                     */

                    this.form.title =
                        row.title || '';

                    this.form.description =
                        row.description || '';


                    /*
                     * Tidak boleh membawa preview
                     * yang lama setelah reload.
                     */

                    this.clearImage();


                } else {

                    /* ----------------------------------------------
                       DATABASE MASIH KOSONG
                    ---------------------------------------------- */

                    this.exists = false;


                    this.original = {

                        title: '',

                        description: ''

                    };


                    this.imageUrl = '';


                    /*
                     * Form tetap tersedia sebagai draft lokal.
                     * Tidak melakukan INSERT apa pun.
                     */

                    this.form.title = '';

                    this.form.description = '';


                    this.clearImage();

                }


            } catch (e) {

                console.error(
                    TAG,
                    e
                );


                this.notify(
                    false,
                    e.message
                );


            } finally {

                this.loading = false;

            }

        },


        /* ==========================================================
           SAVE
        =========================================================== */

        async save() {

            const title =
                this.form.title.trim();


            const description =
                this.form.description.trim();


            /* ======================================================
               TITLE
            ======================================================= */

            if (!title) {

                this.notify(
                    false,
                    'Judul harus diisi!'
                );

                return;

            }


            /* ======================================================
               DESCRIPTION
            ======================================================= */

            if (!description) {

                this.notify(
                    false,
                    'Deskripsi harus diisi!'
                );

                return;

            }


            /* ======================================================
               CREATE
               Saat database masih kosong,
               gambar wajib ada.
            ======================================================= */

            if (!this.exists) {

                if (!this.file) {

                    this.notify(
                        false,
                        'Gambar harus diunggah!'
                    );

                    return;

                }

            }


            /* ======================================================
               FORM DATA
            ======================================================= */

            const form =
                new FormData();


            /*
             * Image hanya dikirim jika:
             * - membuat konten baru
             * - atau mengganti image saat update
             */

            if (this.file) {

                form.append(
                    'image_url',
                    this.file
                );

            }


            form.append(
                'title',
                title
            );


            form.append(
                'description',
                description
            );


            /* ======================================================
               CREATE / UPDATE
            ======================================================= */

            let url;


            if (this.exists) {

                /*
                 * UPDATE
                 */

                url =
                    `${base}/update-coming-up-next-content`;


                /*
                 * Laravel method spoofing
                 */

                form.append(
                    '_method',
                    'PUT'
                );


            } else {

                /*
                 * CREATE
                 */

                url =
                    `${base}/create-coming-up-next-content`;

            }


            this.busy = true;


            try {

                const data =
                    await this.call(

                        url,

                        {

                            method: 'POST',

                            body: form

                        }

                    );


                /*
                 * Simpan hasil operasi.
                 */

                this.notify(

                    true,

                    (
                        data &&
                        data.message
                    )

                    ||

                    (
                        this.exists

                            ? 'Konten berhasil diperbarui!'

                            : 'Konten berhasil dibuat!'
                    )

                );


                /*
                 * INI BAGIAN PENTING.
                 *
                 * Setelah CREATE:
                 *
                 * database sudah memiliki row.
                 *
                 * Kita GET ulang.
                 *
                 * load() kemudian menemukan object
                 * dan mengubah exists menjadi true.
                 */

                await this.load();


            } catch (e) {

                console.error(
                    TAG,
                    e
                );


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