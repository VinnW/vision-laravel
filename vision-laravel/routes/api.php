<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeSectionController
};

Route::prefix('visionasurance')->group(function() {

    Route::controller(HomeSectionController::class)->group(function() {
        Route::get('/banners', 'getBanners');
        Route::post('/create-banner', 'createBanner');
        Route::put('/update-banner/{id}', 'updateBanner');
        Route::delete('/delete-banner/{id}', 'deleteBanner');
    });

});