<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\JoinAgentController;
use App\Http\Controllers\CreateAgentController;
use App\Http\Controllers\AgentPageEditController;

/*
|--------------------------------------------------------------------------
| Helper: data Vision Update untuk halaman publik
|--------------------------------------------------------------------------
*/
$visionUpdates = fn () => DB::table('vision_update_section')
    ->orderByDesc('id')
    ->get();

/*
|--------------------------------------------------------------------------
| Helper: data Coming Up Next untuk halaman publik
|--------------------------------------------------------------------------
*/
$comingUpNext = fn () => DB::table('coming_up_next_section')->first();

/*
|--------------------------------------------------------------------------
| PUBLIK
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    $slides = DB::table('home_section')
        ->orderBy('id')
        ->get()
        ->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => "Desain Banner\nHalaman Utama",
                'subtitle' => '5 slide desain gambar otomatis dan manual slide,',
                'image' => $banner->banner_url,
            ];
        })
        ->toArray();

    return view('public.index', compact('slides'));

})->name('home');

Route::get('/about', function () {
    return view('public.about');
})->name('about');

Route::get('/products', function () {
    return view('public.products');
})->name('products');

Route::get('/service', function () {
    return view('public.service');
})->name('service');

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

/*
|--------------------------------------------------------------------------
| Detail Produk Layanan (dinamis dari tabel `products`)
|--------------------------------------------------------------------------
*/
Route::get('/produk/{id?}', [ProductsController::class, 'showDetail'])
    ->whereNumber('id')
    ->name('products.detail');

Route::get('/produk/{id}/katalog', [ProductsController::class, 'downloadCatalog'])
    ->whereNumber('id')
    ->name('products.catalog');

Route::get('/event', function () use ($visionUpdates) {

    return view('public.sections.event', [
        'updates' => $visionUpdates(),
    ]);

})->name('event');

Route::get('/event/update', function () use ($visionUpdates) {

    return view('public.sections.update', [
        'updates' => $visionUpdates(),
        'standalone' => true,
    ]);

})->name('event.update');

Route::get('/event/coming', function () use ($comingUpNext) {

    return view('public.sections.coming', [
        'coming' => $comingUpNext(),
        'standalone' => true,
    ]);

})->name('event.coming');

/*
|--------------------------------------------------------------------------
| Join Agent
|--------------------------------------------------------------------------
*/
Route::get('/join-agent', function () {
    return view('public.sections.join-agent');
})->name('join-agent');

// Kirim form Join Agent. Sama seperti JoinAgentController@submitForm,
// ditambah username otomatis (primary key join_agent), mis. justinewijaya-k3f9
Route::post('/join-agent', function (\Illuminate\Http\Request $request) {

    $fields = [
        'nama'       => 'Nama',
        'email'      => 'Email',
        'kota'       => 'Kota',
        'no_telepon' => 'No Telepon',
        'pesan'      => 'Pesan',
    ];

    foreach ($fields as $field => $label) {
        if (!$request->filled($field)) {
            return response()->json($label . ' harus diisi!', 400);
        }
    }

    if (!filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
        return response()->json('Email tidak valid!', 400);
    }

    $base = Str::slug((string) $request->input('nama'), '');
    $base = substr($base !== '' ? $base : 'calon', 0, 20);

    do {
        $username = $base . '-' . Str::lower(Str::random(4));
    } while (DB::table('join_agent')->where('username', $username)->exists());

    $hasil = DB::table('join_agent')->insert([
        'username'   => $username,
        'nama'       => $request->input('nama'),
        'email'      => $request->input('email'),
        'kota'       => $request->input('kota'),
        'no_telepon' => $request->input('no_telepon'),
        'pesan'      => $request->input('pesan'),
    ]);

    return response()->json(['Form berhasil di submit!', $hasil, 200]);

})->name('join-agent.submit');

/*
|--------------------------------------------------------------------------
| LOGIN & EDIT AGEN
|--------------------------------------------------------------------------
*/
Route::get('/agent/login', function () {
    return view('public.sections.agent-login', [
        'loginAction' => url('/agent/login'),
    ]);
})->name('public.agent-login.page');

Route::post('/agent/login', function (\Illuminate\Http\Request $request) {

    $username = strtolower(trim((string) $request->input('username')));
    $password = (string) $request->input('password');

    $agent  = DB::table('create_agent')->where('username', $username)->first();
    $stored = (string) ($agent->password ?? '');

    // Mendukung password hash (bcrypt) maupun teks biasa; password kosong (hasil reset) ditolak
    $ok = $agent && $stored !== '' && $password !== '' && (
        str_starts_with($stored, '$2y$')
            ? Hash::check($password, $stored)
            : hash_equals($stored, $password)
    );

    if (!$ok) {
        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    $request->session()->regenerate();
    $request->session()->put('agent_username', $agent->username);

    return redirect()->route('agent.edit');

})->name('agent.login.web');

Route::post('/agent/logout', function (\Illuminate\Http\Request $request) {
    $request->session()->forget('agent_username');
    $request->session()->regenerateToken();
    return redirect()->route('public.agent-login.page');
})->name('agent.logout');

// Hanya agen yang sudah login
Route::group([], function () {

    $guard = function () {
        if (!session('agent_username')) {
            return request()->expectsJson()
                ? response()->json('Silakan login terlebih dahulu.', 401)
                : redirect()->route('public.agent-login.page');
        }
        return null;
    };

    Route::get('/agent/edit', function () use ($guard) {
        if ($resp = $guard()) return $resp;

        $username = session('agent_username');

        // Pastikan baris profil agen ada
        DB::table('agent_page_edit')->updateOrInsert(['username' => $username], []);

        // Alias kolom tabel (description, image, link_wa) ke nama yang dipakai view
        $agent = DB::table('agent_page_edit')
            ->where('username', $username)
            ->select(
                'username',
                'name',
                'description as bio',
                'image as image_url',
                'link_wa as wa_link'
            )
            ->first();

        return view('agent.edit', [
            'agent'     => $agent,
            'updateUrl' => url('/agent/edit'),
            'backUrl'   => url('/' . $username),
        ]);
    })->name('agent.edit');

    // Pengecekan login untuk simpan ada di AgentPageEditController@editAgent (401 bila belum login)
    Route::put('/agent/edit', [AgentPageEditController::class, 'editAgent'])
        ->name('agent.edit.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('admin.login');
})->name('admin.login.page');

Route::post('/login', [AdminLoginController::class, 'login'])
    ->name('admin.login');

Route::post('/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');

Route::middleware('auth:admin')->group(function () {

    Route::view('/dashboard', 'admin.admin_dashboard')
        ->name('admin.dashboard');

    // Pengajuan Join Agent (dipakai agent-panel)
    Route::get('/join-agent-forms', [JoinAgentController::class, 'getForm'])
        ->name('join-agent.forms');

    Route::delete('/join-agent-forms/{nama}', [JoinAgentController::class, 'deleteForm'])
        ->name('join-agent.forms.delete');

    // Akun Agen (dipakai agent-panel)
    Route::get('/create-agents', [CreateAgentController::class, 'getAgents'])
        ->name('create-agent.list');

    Route::post('/create-agent', [CreateAgentController::class, 'createAgent'])
        ->name('create-agent.store');

    Route::post('/create-agent/{username}/reset', [CreateAgentController::class, 'resetPasswordAgent'])
        ->name('create-agent.reset');

    Route::delete('/create-agent/{username}', [CreateAgentController::class, 'deleteAgent'])
        ->name('create-agent.delete');

    // PREVIEW SEMENTARA: User Panel sebagai halaman penuh
    Route::get('/agent-preview/panel', function () {
        return view('agent.preview', [
            'section'   => 'admin.sections.agent-panel',
            'note'      => 'User Panel admin',
            'bodyClass' => 'bg-slate-50',
            'data'      => [],
        ]);
    })->name('agent.preview.panel');

});

/*
|--------------------------------------------------------------------------
| Halaman Agen (publik) → /{username}
|--------------------------------------------------------------------------
| WAJIB di paling bawah file agar tidak menimpa route lain.
*/
Route::get('/{username}', function (string $username) {

    $agent = DB::table('create_agent as c')
        ->leftJoin('agent_page_edit as p', 'p.username', '=', 'c.username')
        ->where('c.username', $username)
        ->select(
            'c.username',
            'p.name',
            'p.description as bio',
            'p.image as image_url',
            'p.link_wa as wa_link'
        )
        ->first();

    abort_if(!$agent, 404);

    return view('agent.page', [
        'agent'   => $agent,
        'isOwner' => session('agent_username') === $agent->username,
    ]);

})->where('username', '[a-z0-9]{3,30}')->name('agent.public');