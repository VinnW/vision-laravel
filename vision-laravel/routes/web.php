<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminLoginController;

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

Route::get('/event', function () {
    return view('public.event');
})->name('event');

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