<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin — Vision Asurance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 antialiased">

<div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6">

    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 -top-32 h-96 w-96 rounded-full bg-[#F2A93B]/25 blur-3xl"></div>
        <div class="absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-indigo-400/20 blur-3xl"></div>
    </div>

    <div
        x-data="loginForm()"
        class="relative w-full max-w-md rounded-3xl border border-white/60 bg-white/80 p-8 shadow-[0_30px_80px_-30px_rgba(30,41,59,0.25)] backdrop-blur"
    >
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-linear-to-br from-[#F2A93B] to-[#E8823C] text-white shadow-lg">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3 4 6v6c0 4.4 3.4 8.4 8 9 4.6-.6 8-4.6 8-9V6l-8-3Z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-indigo-700">Admin Panel</h1>
            <p class="mt-1 text-sm text-slate-500">Masuk untuk mengelola konten website</p>
        </div>

        <div x-show="error" x-cloak
             class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
             x-text="error"></div>

        <form @submit.prevent="submit()" class="space-y-5">
            <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">Username</label>
                <input id="username" x-model="username" type="text" required autofocus autocomplete="username"
                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                       placeholder="username">
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <input id="password" x-model="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-800 outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                           placeholder="••••••••">
                    <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-slate-400 hover:text-indigo-600"
                            x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
            </div>

            <button type="submit" :disabled="loading"
                    class="w-full rounded-full bg-linear-to-r from-[#F2A93B] to-[#E8823C] px-8 py-3.5 text-sm font-semibold text-white shadow-[0_18px_40px_-15px_rgba(232,130,60,0.55)] transition-all duration-200 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                <span x-text="loading ? 'Memproses…' : 'Masuk'"></span>
            </button>
        </form>
    </div>
</div>

<script>
function loginForm() {
    return {
        username: '', password: '', show: false, loading: false, error: '',

        async submit() {
            this.error = '';
            this.loading = true;
            try {
                const res = await fetch('{{ url('visionasurance/login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ username: this.username, password: this.password }),
                });
                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    this.error = data.message || 'Login gagal.';
                    return;
                }

                sessionStorage.setItem('admin_user', this.username);
                window.location.href = '{{ url('visionasurance/dashboard') }}';
            } catch (e) {
                this.error = 'Tidak dapat terhubung ke server.';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
</body>
</html>