<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeSectionController,
    AdminLoginController,
    ComingUpNextController,
    VisionUpdateController,
    EventsController
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

    Route::controller(VisionUpdateController::class)->group(function() {
        Route::get('/vision-update', 'getContent');
        Route::post('/create-vision-update', 'createContent');
        Route::put('/update-vision-update-image/{id}/{image_type}', 'updateImageContent');
        Route::put('/update-vision-update-text/{id}', 'updateTextContent');
        Route::delete('/delete-vision-update/{id}', 'deleteContent');
        Route::delete('/delete-vision-update-image/{id}/{image_type}', 'deleteImage');
    });

    Route::controller(EventsController::class)->group(function() {
        Route::get('/event', 'getEvent');
        Route::post('/create-event', 'createEvent');
        Route::post('/update-event', 'updateEvent');
    });

});