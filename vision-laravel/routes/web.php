<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\JoinAgentController;
use App\Http\Controllers\CreateAgentController;

/*
|--------------------------------------------------------------------------
| Helper: data Vision Update untuk halaman publik
|--------------------------------------------------------------------------
| Data yang sama dengan yang dikelola di vision-update-panel
| (terbaru di atas, sesuai VisionUpdateController::getContent).
*/
$visionUpdates = fn () => DB::table('vision_update_section')
    ->orderByDesc('id')
    ->get();

/*
|--------------------------------------------------------------------------
| Helper: data Coming Up Next untuk halaman publik
|--------------------------------------------------------------------------
| Hanya 1 baris (gambar, judul, deskripsi), null bila belum diisi.
*/
$comingUpNext = fn () => DB::table('coming_up_next_section')->first();

/*
|--------------------------------------------------------------------------
| Helper SEMENTARA: data agen contoh untuk preview tampilan
|--------------------------------------------------------------------------
| Hanya dipakai route preview di bawah. Hapus bersama route preview
| bila halaman agen sudah memakai data asli dari database.
| Isi 'image_url' dengan path gambar (mis. 'storage/agents/foto.webp')
| untuk melihat tampilan dengan foto.
*/
$previewAgent = fn () => (object) [
    'username'  => 'justinewijaya',
    'name'      => 'Justine Wijaya',
    'bio'       => "Dengan pengalaman dalam membantu kebutuhan perlindungan asuransi, saya telah melayani lebih dari 100 nasabah hingga saat ini dan menjadi mitra bagi banyak individu, keluarga, maupun profesional dalam merencanakan perlindungan mereka.\n\nDidukung oleh layanan yang profesional, responsif, dan personal, saya siap mendampingi setiap langkah mulai dari konsultasi, pemilihan perlindungan, hingga follow-up layanan dan pendampingan klaim.",
    'image_url' => '',
    'wa_link'   => '081234567890',
];

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

Route::get('/agent/login', function () {
    return view('public.sections.agent-login');
})->name('public.agent-login.page');

    // Akun Agen (dipakai agent-panel)
    Route::get('/create-agents', [CreateAgentController::class, 'getAgents'])
        ->name('create-agent.list');

    Route::post('/create-agent', [CreateAgentController::class, 'createAgent'])
        ->name('create-agent.store');

    Route::post('/create-agent/{username}/reset', [CreateAgentController::class, 'resetPasswordAgent'])
        ->name('create-agent.reset');

    Route::delete('/create-agent/{username}', [CreateAgentController::class, 'deleteAgent'])
        ->name('create-agent.delete');

/*
|--------------------------------------------------------------------------
| Join Agent
|--------------------------------------------------------------------------
| Tujuan tombol "Join Agent" di hero (route('join-agent')).
*/
Route::get('/join-agent', function () {
    return view('public.sections.join-agent');
})->name('join-agent');

Route::post('/join-agent', [JoinAgentController::class, 'submitForm'])
    ->name('join-agent.submit');

/*
|--------------------------------------------------------------------------
| AGENT (PREVIEW SEMENTARA, memakai data contoh)
|--------------------------------------------------------------------------
| Buka satu per satu untuk mengecek tampilan:
|
|   /agent-preview            → Dashboard agen
|   /agent/dashboard          → Dashboard agen (alamat final yang dipakai tombol "Kembali")
|   /agent/edit               → Halaman edit profil agen
|   /agent-preview/page       → Halaman agen, tampilan PEMILIK (ada bar & tombol edit)
|   /agent-preview/customer   → Halaman agen, tampilan CUSTOMER (tanpa tombol edit)
|   /agent-preview/panel      → User Panel admin (harus login admin dulu)
|
| Nama route agent.edit & agent.dashboard sengaja dibuat sekarang karena
| dipakai oleh tombol di view. Saat backend siap, ganti closure-nya dengan
| controller + middleware agen, lalu hapus route /agent-preview/*.
*/
Route::get('/agent-preview', function () use ($previewAgent) {
    return view('agent.agent_dashboard', [
        'agent'      => $previewAgent(),
        'previewUrl' => route('agent.preview.page'),
    ]);
})->name('agent.preview');

Route::get('/agent/dashboard', function () use ($previewAgent) {
    return view('agent.agent_dashboard', [
        'agent'      => $previewAgent(),
        'previewUrl' => route('agent.preview.page'),
    ]);
})->name('agent.dashboard');

Route::get('/agent/edit', function () use ($previewAgent) {
    return view('agent.preview', [
        'section' => 'agent.sections.agent-edit',
        'note'    => 'Edit profil agen (data contoh, tombol simpan belum terhubung ke agen yang login)',
        'data'    => ['agent' => $previewAgent()],
    ]);
})->name('agent.edit');

Route::get('/agent-preview/page', function () use ($previewAgent) {
    return view('agent.preview', [
        'section' => 'agent.sections.agent',
        'note'    => 'Halaman agen, tampilan PEMILIK',
        'data'    => ['agent' => $previewAgent(), 'isOwner' => true],
    ]);
})->name('agent.preview.page');

Route::get('/agent-preview/customer', function () use ($previewAgent) {
    return view('agent.preview', [
        'section' => 'agent.sections.agent',
        'note'    => 'Halaman agen, tampilan CUSTOMER (tanpa tombol edit)',
        'data'    => ['agent' => $previewAgent(), 'isOwner' => false],
    ]);
})->name('agent.preview.customer');

    // Pengajuan Join Agent (dipakai agent-panel)
    Route::get('/join-agent-forms', [JoinAgentController::class, 'getForm'])
        ->name('join-agent.forms');

    Route::delete('/join-agent-forms/{username}', [JoinAgentController::class, 'deleteForm'])
        ->name('join-agent.forms.delete');

/*
|--------------------------------------------------------------------------
| Detail Produk Layanan (dinamis dari tabel `products`)
|--------------------------------------------------------------------------
| /produk/{id}         → HANYA produk yang diklik lewat "Pelajari Lebih"
| /produk              → tanpa id: menampilkan produk pertama
| /produk/{id}/katalog → mengunduh katalog PDF terbaru produk tersebut
*/
Route::get('/produk/{id?}', [ProductsController::class, 'showDetail'])
    ->whereNumber('id')
    ->name('products.detail');

Route::get('/produk/{id}/katalog', [ProductsController::class, 'downloadCatalog'])
    ->whereNumber('id')
    ->name('products.catalog');

Route::get('/service', function () {
    return view('public.service');
})->name('service');

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

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');


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

    // PREVIEW SEMENTARA: User Panel sebagai halaman penuh (hapus setelah di-include ke admin_dashboard)
    Route::get('/agent-preview/panel', function () {
        return view('agent.preview', [
            'section'   => 'admin.sections.agent-panel',
            'note'      => 'User Panel admin (data contoh)',
            'bodyClass' => 'bg-slate-50',
            'data'      => [],
        ]);
    })->name('agent.preview.panel');

});