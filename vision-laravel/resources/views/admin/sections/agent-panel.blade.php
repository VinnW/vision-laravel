{{--
    User Panel (Admin) → isi menu "User Panel" di admin_dashboard
    Lokasi saran: resources/views/admin/sections/agent-panel.blade.php
    Pemakaian   : @include('admin.sections.agent-panel') di dalam area konten "User Panel"

    Sub-menu:
      1. Pengajuan Join Agent → JoinAgentController (tabel join_agent, primary key: username)
      2. Akun Agen            → CreateAgentController (tabel create_agent)
    Admin TIDAK mengedit profil agen (foto, nama, bio, WA). Itu hak agen sendiri.

    ─── JoinAgentController ───
    GET    /join-agent-forms               → getForm
    DELETE /join-agent-forms/{username}    → deleteForm (username = pengenal otomatis dari saat pendaftaran)
    Status "Sudah dihubungi" disimpan di localStorage browser admin.

    ─── CreateAgentController ───
    GET    /create-agents                      → getAgents          [{ username, link_page, created_at? }]
    POST   /create-agent                       → createAgent        ← { username, password, link_page }
    POST   /create-agent/{username}/reset      → resetPasswordAgent (password dikosongkan oleh backend)
    DELETE /create-agent/{username}            → deleteAgent
--}}

<script>
    function agentPanel() {
        const BASE = @js(rtrim(url('/'), '/'));
        const HOST = @js(parse_url(url('/'), PHP_URL_HOST));
        const LOGIN_URL = @js(url('/agent/login'));
        const CSRF = @js(csrf_token());
        const CONTACTED_KEY = 'joinAgentContacted';

        const RESERVED = [
            'admin', 'login', 'logout', 'agent', 'agents', 'join-agent', 'joinagent', 'produk', 'products',
            'about', 'service', 'event', 'contact', 'dashboard', 'api', 'storage', 'visionasurance',
            'edit', 'register', 'password',
        ];

        return {
            endpoints: {
                applications:  @js(url('/join-agent-forms')),
                application:   id => @js(url('/join-agent-forms')) + '/' + encodeURIComponent(id),

                agents:        @js(url('/create-agents')),
                createAgent:   @js(url('/create-agent')),
                resetPassword: u => @js(url('/create-agent')) + '/' + encodeURIComponent(u) + '/reset',
                deleteAgent:   u => @js(url('/create-agent')) + '/' + encodeURIComponent(u),
            },

            tab: 'applications',
            loading: true,
            loadError: '',
            applications: [],
            agents: [],
            contactedIds: [],

            form: { username: '', password: '' },
            showPassword: false,
            submitting: false,
            formError: '',
            created: null,
            fromApp: null,      // pengajuan yang sedang dijadikan akun

            modal: null,        // { type: 'reset'|'delete-agent'|'delete-application', target }
            modalBusy: false,
            modalError: '',

            toast: '',
            toastTimer: null,

            host: HOST,

            /* ---------- lifecycle ---------- */
            async init() {
                this.contactedIds = this.readContacted();
                await this.load();
            },

            async load() {
                this.loading = true;
                this.loadError = '';

                try {
                    const [apps, agents] = await Promise.all([
                        this.api('GET', this.endpoints.applications),
                        this.api('GET', this.endpoints.agents),
                    ]);

                    const appRows = Array.isArray(apps) ? apps : (apps && apps.data) || [];
                    const agentRows = Array.isArray(agents) ? agents : (agents && agents.data) || [];

                    this.applications = appRows.map(r => this.mapApplication(r));
                    this.agents = agentRows.map(r => this.mapAgent(r));
                } catch (e) {
                    this.loadError = e.message || 'Data tidak dapat dimuat.';
                } finally {
                    this.loading = false;
                }
            },

            async api(method, url, body) {
                const res = await fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: body ? JSON.stringify(body) : undefined,
                });

                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    throw new Error(typeof data === 'string' ? data : (data && data.message) || 'Terjadi kesalahan. Coba lagi.');
                }

                return data;
            },

            /* ---------- mapping ---------- */
            mapApplication(r) {
                return {
                    id: r.username,
                    name: r.nama || '',
                    phone: r.no_telepon || '',
                    email: r.email || '',
                    city: r.kota || '',
                    message: r.pesan || '',
                    status: this.contactedIds.includes(r.username) ? 'contacted' : 'new',
                    created_at: r.created_at || null,
                };
            },

            mapAgent(r) {
                return {
                    username: r.username,
                    link_page: r.link_page || this.linkFor(r.username),
                    created_at: r.created_at || null,
                };
            },

            readContacted() {
                try {
                    const v = JSON.parse(localStorage.getItem(CONTACTED_KEY) || '[]');
                    return Array.isArray(v) ? v : [];
                } catch (e) {
                    return [];
                }
            },

            saveContacted() {
                try {
                    localStorage.setItem(CONTACTED_KEY, JSON.stringify(this.contactedIds));
                } catch (e) {}
            },

            /* ---------- helpers ---------- */
            get newCount() {
                return this.applications.filter(a => a.status === 'new').length;
            },

            linkFor(username) {
                return BASE + '/' + username;
            },

            shortLink(username) {
                return this.host + '/' + username;
            },

            waHref(phone) {
                let d = String(phone || '').replace(/\D/g, '');
                if (d.startsWith('0')) d = '62' + d.slice(1);
                return d ? 'https://wa.me/' + d : '#';
            },

            fmtDate(s) {
                if (!s) return '';
                const d = new Date(s);
                return isNaN(d) ? '' : d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            slug(s) {
                return String(s || '').toLowerCase().replace(/[^a-z0-9]/g, '').slice(0, 30);
            },

            notify(msg) {
                this.toast = msg;
                clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => this.toast = '', 2600);
            },

            async copy(text, label) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.notify((label || 'Teks') + ' tersalin');
                } catch (e) {
                    this.notify('Tidak bisa menyalin. Salin manual.');
                }
            },

            /* ---------- form buat akun ---------- */
            get usernameError() {
                const u = this.form.username;
                if (!u) return '';
                if (!/^[a-z0-9]+$/.test(u)) return 'Hanya huruf kecil dan angka, tanpa spasi.';
                if (u.length < 3) return 'Minimal 3 karakter.';
                if (u.length > 30) return 'Maksimal 30 karakter.';
                if (RESERVED.includes(u)) return 'Username ini dipakai sistem. Pilih yang lain.';
                if (this.agents.some(a => a.username === u)) return 'Username sudah dipakai agen lain.';
                return '';
            },

            get canCreate() {
                return this.form.username.length >= 3 && !this.usernameError && this.form.password.length >= 8 && !this.submitting;
            },

            generatePassword() {
                const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
                const arr = new Uint32Array(10);
                crypto.getRandomValues(arr);
                this.form.password = Array.from(arr, n => chars[n % chars.length]).join('');
                this.showPassword = true;
            },

            startAccountFrom(app) {
                this.fromApp = app;
                this.form.username = this.slug(app.name);
                this.form.password = '';
                this.created = null;
                this.formError = '';
                this.tab = 'accounts';
                this.$nextTick(() => this.$refs.username && this.$refs.username.focus());
            },

            async createAgent() {
                if (!this.canCreate) return;
                this.submitting = true;
                this.formError = '';

                const payload = {
                    username: this.form.username,
                    password: this.form.password,
                    link_page: this.linkFor(this.form.username),
                };

                try {
                    const res = await this.api('POST', this.endpoints.createAgent, payload);

                    // CreateAgentController@createAgent mengembalikan [pesan, hasil_insert, 200]
                    if (Array.isArray(res) && res[1] === false) {
                        throw new Error('Akun gagal dibuat.');
                    }

                    this.agents.unshift(this.mapAgent({ username: payload.username, link_page: payload.link_page, created_at: new Date().toISOString() }));
                    this.created = { username: payload.username, password: payload.password };
                    this.form = { username: '', password: '' };
                    this.showPassword = false;

                    // Hapus pesan pengajuan asal akun ini
                    if (this.fromApp) {
                        const app = this.fromApp;
                        this.fromApp = null;

                        try {
                            await this.api('DELETE', this.endpoints.application(app.id));
                            this.applications = this.applications.filter(a => a.id !== app.id);
                            this.contactedIds = this.contactedIds.filter(id => id !== app.id);
                            this.saveContacted();
                        } catch (e) {
                            this.notify('Akun dibuat, tetapi pengajuan gagal dihapus.');
                        }
                    }
                } catch (e) {
                    this.formError = /duplicate/i.test(e.message) ? 'Username sudah dipakai agen lain.' : e.message;
                } finally {
                    this.submitting = false;
                }
            },

            credentialMessage(c) {
                return [
                    'Halo, akun agen Vision Anda sudah dibuat.',
                    '',
                    'Halaman Anda: ' + this.linkFor(c.username),
                    'Login di: ' + LOGIN_URL,
                    'Username: ' + c.username,
                    'Password sementara: ' + c.password,
                    '',
                    'Silakan login, lalu lengkapi foto, nama, pengalaman, dan link WhatsApp Anda.',
                ].join('\n');
            },

            /* ---------- aksi pengajuan ---------- */
            markContacted(app) {
                if (!this.contactedIds.includes(app.id)) this.contactedIds.push(app.id);
                this.saveContacted();
                app.status = 'contacted';
                this.notify('Ditandai sudah dihubungi');
            },

            /* ---------- modal ---------- */
            openModal(type, target) {
                this.modal = { type, target };
                this.modalError = '';
                this.modalBusy = false;
            },

            closeModal() {
                if (!this.modalBusy) this.modal = null;
            },

            async confirmModal() {
                if (!this.modal) return;
                const m = this.modal;
                this.modalBusy = true;
                this.modalError = '';

                try {
                    if (m.type === 'reset') {
                        const res = await this.api('POST', this.endpoints.resetPassword(m.target.username));

                        if (Array.isArray(res) && res[1] === 0) {
                            throw new Error('Tidak ada data yang diubah.');
                        }

                        this.notify('Password direset');
                    }

                    if (m.type === 'delete-agent') {
                        const res = await this.api('DELETE', this.endpoints.deleteAgent(m.target.username));

                        if (Array.isArray(res) && res[1] === 0) {
                            throw new Error('Tidak ada data yang terhapus.');
                        }

                        this.agents = this.agents.filter(a => a.username !== m.target.username);
                        this.notify('Akun dihapus');
                    }

                    if (m.type === 'delete-application') {
                        const res = await this.api('DELETE', this.endpoints.application(m.target.id));

                        // JoinAgentController@deleteForm mengembalikan [pesan, jumlah_terhapus, 200]
                        if (Array.isArray(res) && res[1] === 0) {
                            throw new Error('Tidak ada data yang terhapus.');
                        }

                        this.applications = this.applications.filter(a => a.id !== m.target.id);
                        this.contactedIds = this.contactedIds.filter(id => id !== m.target.id);
                        this.saveContacted();
                        this.notify('Pengajuan dihapus');
                    }

                    this.modalBusy = false;
                    this.modal = null;
                } catch (e) {
                    this.modalError = e.message;
                    this.modalBusy = false;
                }
            },
        };
    }
</script>

<div
    x-data="agentPanel()"
    x-init="init()"
    @keydown.escape.window="closeModal()"
    class="space-y-6 text-slate-800"
>

    {{-- Sub-menu --}}
    <div class="inline-flex rounded-xl border border-slate-200 bg-white p-1" role="tablist" aria-label="Sub-menu User Panel">
        <button
            type="button" role="tab" :aria-selected="tab === 'applications'"
            @click="tab = 'applications'"
            :class="tab === 'applications' ? 'bg-[#EEF1FF] text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
        >
            Pengajuan Join Agent
            <span x-show="newCount > 0" x-text="newCount" class="rounded-full bg-[#1B3478] px-2 py-0.5 text-[11px] font-bold leading-none text-white"></span>
        </button>

        <button
            type="button" role="tab" :aria-selected="tab === 'accounts'"
            @click="tab = 'accounts'"
            :class="tab === 'accounts' ? 'bg-[#EEF1FF] text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
        >
            Akun Agen
        </button>
    </div>

    {{-- Status memuat / gagal --}}
    <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-sm text-slate-500">Memuat data...</div>

    <div x-show="!loading && loadError" x-cloak class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        <span x-text="loadError"></span>
        <button type="button" @click="load()" class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-semibold hover:bg-red-100">Coba lagi</button>
    </div>


    {{-- ===================================================== --}}
    {{-- 1. PENGAJUAN JOIN AGENT --}}
    {{-- ===================================================== --}}
    <section x-show="!loading && !loadError && tab === 'applications'" x-cloak class="space-y-3" role="tabpanel">

        <div x-show="applications.length === 0" class="rounded-2xl border-2 border-dashed border-slate-200 bg-white px-6 py-14 text-center text-sm text-slate-500">
            Belum ada pengajuan Join Agent.
        </div>

        <template x-for="app in applications" :key="app.id">
            <article class="rounded-2xl border border-slate-200 bg-white p-5">

                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-semibold text-slate-900" x-text="app.name"></h3>
                            <span
                                x-text="app.status === 'new' ? 'Baru' : 'Sudah dihubungi'"
                                :class="app.status === 'new' ? 'bg-[#EEF1FF] text-[#1B3478]' : 'bg-slate-100 text-slate-500'"
                                class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                            ></span>
                        </div>

                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-sm text-slate-500">
                            <span x-text="app.phone"></span>
                            <span x-show="app.email" x-text="app.email"></span>
                            <span x-show="app.city" x-text="app.city"></span>
                        </div>
                    </div>

                    <span class="text-xs text-slate-400" x-text="fmtDate(app.created_at)"></span>
                </div>

                <p x-show="app.message" x-text="app.message" class="mt-3 max-w-2xl whitespace-pre-line rounded-xl bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-700"></p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <a :href="waHref(app.phone)" target="_blank" rel="noopener noreferrer"
                       class="rounded-lg border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                        Chat WhatsApp
                    </a>

                    <button type="button" x-show="app.status === 'new'" @click="markContacted(app)"
                            class="rounded-lg border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                        Tandai sudah dihubungi
                    </button>

                    <button type="button" @click="startAccountFrom(app)"
                            class="rounded-lg bg-[#1B3478] px-3.5 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#10127A]">
                        Buat akun agen
                    </button>

                    <button type="button" @click="openModal('delete-application', app)"
                            class="ml-auto rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50">
                        Hapus
                    </button>
                </div>
            </article>
        </template>
    </section>


    {{-- ===================================================== --}}
    {{-- 2. AKUN AGEN --}}
    {{-- ===================================================== --}}
    <section x-show="!loading && !loadError && tab === 'accounts'" x-cloak class="grid items-start gap-6 xl:grid-cols-[380px_minmax(0,1fr)]" role="tabpanel">

        {{-- Kolom kiri: buat akun + hasil --}}
        <div class="space-y-4">

            <form @submit.prevent="createAgent()" novalidate class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="text-base font-semibold text-slate-900">Buat akun agen</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Cukup username dan password sementara. Link halaman agen langsung aktif dari username. Foto, nama, dan pengalaman diisi sendiri oleh agen.
                </p>

                <div class="mt-5">
                    <label for="new-username" class="block text-xs font-semibold text-slate-700">Username</label>
                    <div
                        :class="usernameError ? 'border-red-400' : 'border-slate-200 focus-within:border-[#1B3478]'"
                        class="mt-1.5 flex items-center overflow-hidden rounded-xl border bg-white"
                    >
                        <span class="shrink-0 bg-slate-50 px-3 py-2.5 text-xs text-slate-500" x-text="host + '/'"></span>
                        <input
                            id="new-username" x-ref="username" type="text"
                            x-model="form.username" @input="form.username = form.username.toLowerCase().replace(/\s/g, '')"
                            maxlength="30" autocomplete="off" spellcheck="false" placeholder="justinewijaya"
                            class="min-w-0 flex-1 px-3 py-2.5 text-sm focus:outline-none"
                        />
                    </div>
                    <p x-show="usernameError" x-text="usernameError" class="mt-1.5 text-xs font-medium text-red-600"></p>
                    <p x-show="form.username && !usernameError" class="mt-1.5 break-all text-xs text-slate-500">
                        Link agen: <span class="font-semibold text-slate-700" x-text="shortLink(form.username)"></span>
                    </p>
                </div>

                <div class="mt-4">
                    <label for="new-password" class="block text-xs font-semibold text-slate-700">Password sementara</label>
                    <div class="mt-1.5 flex items-center gap-2">
                        <input
                            id="new-password" :type="showPassword ? 'text' : 'password'"
                            x-model="form.password" autocomplete="new-password" placeholder="Minimal 8 karakter"
                            class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-[#1B3478] focus:outline-none"
                        />
                        <button type="button" @click="showPassword = !showPassword"
                                class="shrink-0 rounded-lg border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                                x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'"></button>
                    </div>
                    <button type="button" @click="generatePassword()" class="mt-2 text-xs font-semibold text-[#1B3478] hover:underline">Buat password acak</button>
                </div>

                <p x-show="formError" x-text="formError" x-cloak class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700"></p>

                <button type="submit" :disabled="!canCreate"
                        class="mt-5 w-full rounded-xl bg-[#1B3478] px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-[#10127A] disabled:cursor-not-allowed disabled:opacity-40">
                    <span x-show="!submitting">Buat akun</span>
                    <span x-show="submitting" x-cloak>Membuat akun...</span>
                </button>
            </form>

            {{-- Hasil: kredensial untuk dikirim ke agen --}}
            <div x-show="created" x-cloak class="rounded-2xl border-2 border-emerald-300 bg-emerald-50 p-5">
                <h3 class="text-sm font-semibold text-emerald-900">Akun siap dikirim ke agen</h3>
                <p class="mt-1 text-xs text-emerald-800">Password hanya tampil sekarang. Salin dan kirim lewat WhatsApp.</p>

                <dl class="mt-4 space-y-2 text-sm" x-show="created">
                    <div>
                        <dt class="text-xs text-emerald-700">Link halaman</dt>
                        <dd class="break-all font-semibold text-emerald-950" x-text="created && linkFor(created.username)"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-emerald-700">Username</dt>
                        <dd class="font-semibold text-emerald-950" x-text="created && created.username"></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-emerald-700">Password sementara</dt>
                        <dd class="font-mono font-semibold text-emerald-950" x-text="created && created.password"></dd>
                    </div>
                </dl>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" @click="copy(credentialMessage(created), 'Pesan')"
                            class="rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-800">Salin pesan untuk agen</button>
                    <button type="button" @click="created = null"
                            class="rounded-lg px-3.5 py-2 text-xs font-semibold text-emerald-800 hover:bg-emerald-100">Tutup</button>
                </div>
            </div>
        </div>


        {{-- Kolom kanan: daftar akun --}}
        <div class="space-y-3">
            <div class="flex items-baseline justify-between">
                <h3 class="text-base font-semibold text-slate-900">Daftar agen</h3>
                <span class="text-xs text-slate-500" x-text="agents.length + ' akun'"></span>
            </div>

            <div x-show="agents.length === 0" class="rounded-2xl border-2 border-dashed border-slate-200 bg-white px-6 py-14 text-center text-sm text-slate-500">
                Belum ada akun agen. Buat yang pertama lewat form di samping.
            </div>

            <template x-for="agent in agents" :key="agent.username">
                <article class="rounded-2xl border border-slate-200 bg-white p-5">

                    <div class="min-w-0">
                        <h4 class="text-base font-semibold text-slate-900" x-text="agent.username"></h4>
                        <div class="mt-1 flex items-center gap-2">
                            <a :href="agent.link_page" target="_blank" rel="noopener noreferrer"
                               class="break-all text-xs text-[#1B3478] hover:underline" x-text="shortLink(agent.username)"></a>
                            <button type="button" @click="copy(agent.link_page, 'Link')"
                                    class="shrink-0 rounded-md border border-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-600 hover:bg-slate-50">Salin</button>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button type="button" @click="openModal('reset', agent)"
                                class="rounded-lg border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Reset password</button>

                        <span x-show="agent.created_at" class="text-xs text-slate-400" x-text="'Dibuat ' + fmtDate(agent.created_at)"></span>

                        <button type="button" @click="openModal('delete-agent', agent)"
                                class="ml-auto rounded-lg px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                    </div>
                </article>
            </template>
        </div>
    </section>


    {{-- ===================================================== --}}
    {{-- MODAL (reset password & konfirmasi hapus) --}}
    {{-- ===================================================== --}}
    <div x-show="modal" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4" @click.self="closeModal()">
        <div role="dialog" aria-modal="true" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.stop>

            <template x-if="modal && modal.type === 'reset'">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Reset password?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Password untuk <span class="font-semibold text-slate-800" x-text="modal.target.username"></span>
                        akan dikosongkan dan password lama tidak berlaku lagi.
                    </p>
                </div>
            </template>

            <template x-if="modal && modal.type === 'delete-agent'">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus akun agen?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Akun <span class="font-semibold text-slate-800" x-text="modal.target.username"></span> dan halamannya
                        (<span x-text="shortLink(modal.target.username)"></span>) akan dihapus permanen.
                    </p>
                </div>
            </template>

            <template x-if="modal && modal.type === 'delete-application'">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Hapus pengajuan?</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Pengajuan dari <span class="font-semibold text-slate-800" x-text="modal.target.name"></span> akan dihapus permanen.
                    </p>
                </div>
            </template>

            <p x-show="modalError" x-text="modalError" class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700"></p>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="closeModal()" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="button" @click="confirmModal()" :disabled="modalBusy"
                        :class="modal && modal.type === 'reset' ? 'bg-[#1B3478] hover:bg-[#10127A]' : 'bg-red-600 hover:bg-red-700'"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40"
                        x-text="modalBusy ? 'Memproses...' : (modal && modal.type === 'reset' ? 'Reset' : 'Hapus')"></button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast" x-cloak x-transition class="fixed bottom-6 right-6 z-50 rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white shadow-lg" role="status" aria-live="polite" x-text="toast"></div>

</div>