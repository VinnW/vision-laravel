<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminLoginController;

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

/*
|--------------------------------------------------------------------------
| Detail Produk Layanan
|--------------------------------------------------------------------------
| Data dari config/products.php (sumber yang sama dengan slider di
| products.blade.php). Link dari slider: /produk#slug-produk
*/
Route::get('/produk', function () {

    return view('public.sections.detail-products', [
        'products' => collect(config('products', [])),
        'standalone' => true,
    ]);

})->name('products.detail');

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

});