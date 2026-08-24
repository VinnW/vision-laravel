<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeSectionController,
    AdminLoginController,
    ComingUpNextController
};

Route::prefix('visionasurance')->group(function() {

    Route::controller(AdminLoginController::class)->group(function() {
        Route::match(['get', 'post'], '/login', 'login');
    });

    Route::controller(HomeSectionController::class)->group(function() {
        Route::get('/banners', 'getBanners');
        Route::post('/create-banner', 'createBanner');
        Route::put('/update-banner/{id}', 'updateBanner');
        Route::delete('/delete-banner/{id}', 'deleteBanner');
    });

    Route::controller(ComingUpNextController::class)->group(function() {
        Route::get('/coming-up-next-content', 'getContent');
        Route::post('/create-coming-up-next-content', 'createContent');
        Route::put('/update-coming-up-next-content', 'updateContent');
    });

});