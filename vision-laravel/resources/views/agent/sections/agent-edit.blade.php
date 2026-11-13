{{--
    Halaman Edit Profil Agen
    Lokasi saran: resources/views/agent/sections/agent-edit.blade.php

    VARIABEL YANG DITERIMA:
      $agent      : object { username, name, bio, image_url, wa_link }  (agen yang sedang login)
      $updateUrl  : (opsional) endpoint simpan. Default: /api/visionasurance/agent-edit/{username}
      $backUrl    : (opsional) tombol "Kembali"

    KONTRAK FORM (untuk backend):
      Dikirim lewat fetch() sebagai FormData, method POST + _method=PUT
      (PUT tidak bisa membawa file di PHP, jadi memakai method spoofing Laravel).
      Field   : image_url (file, opsional), name, bio, wa_link (sudah dinormalisasi jadi https://wa.me/62...)
      Sukses  : status 200
      Gagal   : status 422 dengan { errors: { field: [pesan] } } atau pesan teks/JSON { message }
--}}

@php
    $imageSrc = function (?string $path): string {
        $p = trim((string) $path);

        if ($p === '') {
            return '';
        }

        if (preg_match('#^https?://#', $p) || str_starts_with($p, '/')) {
            return $p;
        }

        if (
            str_starts_with($p, 'storage/') ||
            str_starts_with($p, 'images/') ||
            str_starts_with($p, 'uploads/')
        ) {
            return url($p);
        }

        return url('storage/' . $p);
    };

    $username  = (string) ($agent->username ?? '');
    $updateUrl = $updateUrl ?? url('/api/visionasurance/agent-edit/' . $username);
    $viewUrl   = url('/' . $username);
    $backUrl   = $backUrl ?? (Route::has('agent.dashboard') ? route('agent.dashboard') : url('/agent/dashboard'));
@endphp

<script>
    function agentEditForm(cfg) {
        return {
            name: cfg.name || '',
            bio: cfg.bio || '',
            wa: cfg.wa || '',
            photo: cfg.photo || '',
            file: null,
            bioMax: 1200,
            saving: false,
            saved: false,
            message: '',
            errors: {},

            // 08xx / 628xx / link penuh → https://wa.me/628xx (kosong bila tidak valid)
            get waLink() {
                const v = this.wa.trim();
                if (!v) return '';
                if (/^https?:\/\//i.test(v)) return v;
                let d = v.replace(/\D/g, '');
                if (d.startsWith('0')) d = '62' + d.slice(1);
                return d.length >= 9 && d.length <= 15 ? 'https://wa.me/' + d : '';
            },

            pickPhoto(e) {
                const f = e.target.files[0];
                if (!f) return;

                if (!f.type.startsWith('image/')) {
                    this.errors.image_url = 'File harus berupa gambar (JPG, PNG, atau WebP).';
                    return;
                }

                if (f.size > 4 * 1024 * 1024) {
                    this.errors.image_url = 'Ukuran foto maksimal 4 MB.';
                    return;
                }

                delete this.errors.image_url;
                this.file = f;
                this.photo = URL.createObjectURL(f);
                this.saved = false;
            },

            validate() {
                const err = {};
                if (!this.name.trim()) err.name = 'Nama wajib diisi.';
                if (!this.wa.trim()) err.wa_link = 'Link WhatsApp wajib diisi.';
                else if (!this.waLink) err.wa_link = 'Isi nomor (contoh 0812xxxx) atau link wa.me yang valid.';
                if (this.bio.length > this.bioMax) err.bio = 'Maksimal ' + this.bioMax + ' karakter.';
                this.errors = { ...this.errors, ...err };
                if (!err.name) delete this.errors.name;
                if (!err.wa_link) delete this.errors.wa_link;
                if (!err.bio) delete this.errors.bio;
                return Object.keys(err).length === 0;
            },

            async save() {
                this.message = '';
                this.saved = false;
                if (!this.validate()) return;

                const fd = new FormData();
                fd.append('_method', 'PUT');
                fd.append('name', this.name.trim());
                fd.append('bio', this.bio.trim());
                fd.append('wa_link', this.waLink);
                if (this.file) fd.append('image_url', this.file);

                this.saving = true;

                try {
                    const res = await fetch(cfg.url, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                        body: fd,
                    });

                    const data = await res.json().catch(() => null);

                    if (res.status === 422 && data && data.errors) {
                        Object.keys(data.errors).forEach(k => this.errors[k] = data.errors[k][0]);
                        this.message = 'Periksa kembali isian yang bertanda merah.';
                        return;
                    }

                    if (!res.ok) {
                        this.message = typeof data === 'string' ? data : (data && data.message) || 'Gagal menyimpan. Coba lagi.';
                        return;
                    }

                    this.saved = true;
                    this.file = null;
                    this.message = typeof data === 'string' ? data : 'Profil berhasil disimpan.';
                } catch (e) {
                    this.message = 'Tidak dapat terhubung ke server. Periksa koneksi Anda.';
                } finally {
                    this.saving = false;
                }
            },
        };
    }
</script>

<section
    x-data="agentEditForm(@js([
        'name'  => $agent->name ?? '',
        'bio'   => $agent->bio ?? '',
        'wa'    => $agent->wa_link ?? '',
        'photo' => $imageSrc($agent->image_url ?? null),
        'url'   => $updateUrl,
        'csrf'  => csrf_token(),
    ]))"
    class="relative overflow-hidden bg-white pb-16 pt-28 lg:pb-24 lg:pt-32"
>
    <div class="mx-auto max-w-5xl px-6 lg:px-10">

        {{-- Kepala halaman --}}
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1B2F8F] hover:underline">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
            Kembali
        </a>

        <h1 class="mt-4 text-3xl font-extrabold uppercase tracking-tight text-[#1F4E9E] sm:text-4xl">
            Edit profil
        </h1>
        <p class="mt-2 max-w-lg text-sm font-medium text-[#1B2F8F]">
            Data ini tampil di halaman Anda untuk customer. Hanya Anda yang bisa mengubahnya.
        </p>

        <div class="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ============ FORM ============ --}}
            <form @submit.prevent="save()" novalidate class="space-y-7">

                {{-- Status simpan --}}
                <div x-show="message" x-cloak role="status" aria-live="polite"
                     :class="saved ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-red-300 bg-red-50 text-red-700'"
                     class="rounded-xl border-2 px-4 py-3 text-sm font-medium">
                    <span x-text="message"></span>
                    <a x-show="saved" href="{{ $viewUrl }}" class="ml-1 font-bold underline">Lihat halaman saya</a>
                </div>

                {{-- Foto --}}
                <div>
                    <label class="block text-sm font-bold text-[#10127A]">Foto profil</label>
                    <div class="mt-3 flex items-center gap-5">
                        <div class="h-24 w-20 shrink-0 overflow-hidden rounded-2xl border-4 border-[#6BB0FF] bg-slate-200">
                            <template x-if="photo">
                                <img :src="photo" alt="Foto profil" class="h-full w-full object-cover object-top" />
                            </template>
                            <template x-if="!photo">
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" /></svg>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="inline-block cursor-pointer rounded-full border-2 border-[#10127A] px-5 py-2 text-xs font-semibold text-[#10127A] transition-all duration-200 hover:bg-[#10127A] hover:text-white focus-within:ring-4 focus-within:ring-[#1E3A8A]/30">
                                <span x-text="photo ? 'Ganti foto' : 'Pilih foto'"></span>
                                <input type="file" accept="image/*" class="sr-only" @change="pickPhoto($event)" />
                            </label>
                            <p class="mt-2 text-xs text-slate-500">JPG, PNG, atau WebP. Maksimal 4 MB. Foto setengah badan terlihat paling baik.</p>
                        </div>
                    </div>
                    <p x-show="errors.image_url" x-text="errors.image_url" class="mt-2 text-xs font-medium text-red-600"></p>
                </div>

                {{-- Nama --}}
                <div>
                    <label for="agent-name" class="block text-sm font-bold text-[#10127A]">Nama</label>
                    <input
                        id="agent-name"
                        type="text"
                        x-model="name"
                        maxlength="60"
                        autocomplete="name"
                        placeholder="Contoh: Anindia Putri"
                        :class="errors.name ? 'border-red-400' : 'border-slate-200 focus:border-[#6BB0FF]'"
                        class="mt-2 w-full rounded-xl border-2 px-4 py-3 text-sm text-[#1B2F8F] placeholder:text-slate-400 focus:outline-none"
                    />
                    <p x-show="errors.name" x-text="errors.name" class="mt-1.5 text-xs font-medium text-red-600"></p>
                </div>

                {{-- Pengalaman / deskripsi --}}
                <div>
                    <div class="flex items-baseline justify-between">
                        <label for="agent-bio" class="block text-sm font-bold text-[#10127A]">Pengalaman dan deskripsi</label>
                        <span class="text-xs text-slate-500" :class="bio.length > bioMax && 'font-bold text-red-600'" x-text="bio.length + ' / ' + bioMax"></span>
                    </div>
                    <textarea
                        id="agent-bio"
                        x-model="bio"
                        rows="8"
                        placeholder="Ceritakan pengalaman Anda membantu nasabah. Pisahkan paragraf dengan satu baris kosong."
                        :class="errors.bio ? 'border-red-400' : 'border-slate-200 focus:border-[#6BB0FF]'"
                        class="mt-2 w-full resize-y rounded-xl border-2 px-4 py-3 text-sm leading-relaxed text-[#1B2F8F] placeholder:text-slate-400 focus:outline-none"
                    ></textarea>
                    <p x-show="errors.bio" x-text="errors.bio" class="mt-1.5 text-xs font-medium text-red-600"></p>
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label for="agent-wa" class="block text-sm font-bold text-[#10127A]">Nomor atau link WhatsApp</label>
                    <input
                        id="agent-wa"
                        type="text"
                        x-model="wa"
                        inputmode="tel"
                        placeholder="0812xxxxxxxx"
                        :class="errors.wa_link ? 'border-red-400' : 'border-slate-200 focus:border-[#6BB0FF]'"
                        class="mt-2 w-full rounded-xl border-2 px-4 py-3 text-sm text-[#1B2F8F] placeholder:text-slate-400 focus:outline-none"
                    />
                    <p x-show="errors.wa_link" x-text="errors.wa_link" class="mt-1.5 text-xs font-medium text-red-600"></p>
                    <p x-show="waLink && !errors.wa_link" class="mt-1.5 break-all text-xs text-slate-500">
                        Tombol Hubungi Kami akan membuka <span class="font-semibold text-[#1B2F8F]" x-text="waLink"></span>
                    </p>
                </div>

                {{-- Aksi --}}
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="rounded-full bg-linear-to-r from-[#2F57A8] to-[#1B3478] px-9 py-3 text-sm font-medium text-white shadow-[0_10px_22px_-10px_rgba(27,52,120,0.7)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_14px_26px_-10px_rgba(27,52,120,0.8)] disabled:cursor-wait disabled:opacity-60 disabled:hover:translate-y-0"
                    >
                        <span x-show="!saving">Simpan perubahan</span>
                        <span x-show="saving" x-cloak>Menyimpan...</span>
                    </button>

                    <a href="{{ $backUrl }}" class="text-sm font-semibold text-[#1B2F8F] hover:underline">Batal</a>
                </div>
            </form>


            {{-- ============ PRATINJAU ============ --}}
            <aside class="hidden lg:block" aria-label="Pratinjau">
                <div class="sticky top-28 rounded-3xl border-4 border-[#6BB0FF] bg-[#F7FAFF] p-5">
                    <p class="text-xs font-bold text-[#1B2F8F]">Pratinjau di halaman Anda</p>

                    <div class="mt-4 aspect-4/5 overflow-hidden rounded-2xl bg-slate-200">
                        <template x-if="photo">
                            <img :src="photo" alt="" class="h-full w-full object-cover object-top" />
                        </template>
                        <template x-if="!photo">
                            <div class="flex h-full w-full items-center justify-center">
                                <svg class="h-12 w-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" /></svg>
                            </div>
                        </template>
                    </div>

                    <p class="mt-4 break-words text-xl font-extrabold uppercase leading-tight text-[#10127A]">
                        Halo, saya <span x-text="name.trim() || 'Nama Anda'"></span>
                    </p>
                    <p class="mt-1 text-[11px] font-bold uppercase text-[#10127A]">Official Trusted Agent Vision</p>
                    <p class="mt-3 line-clamp-5 whitespace-pre-line text-xs leading-relaxed text-slate-700" x-text="bio.trim() || 'Pengalaman Anda tampil di sini.'"></p>
                </div>
            </aside>

        </div>
    </div>
</section>