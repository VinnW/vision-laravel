<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeSectionController,
    AdminLoginController,
    ComingUpNextController,
    VisionUpdateController,
    EventsController,
    AgentPageEditController,
    AgentLoginController
};

Route::prefix('visionasurance')->group(function () {

    /* ---------- AUTH ---------- */
    Route::match(['get', 'post'], '/login', [AdminLoginController::class, 'login'])
        ->name('admin.login');

    Route::post('/logout', [AdminLoginController::class, 'logout'])
        ->name('admin.logout');

    Route::match(['get', 'post'], '/agent-login', [AgentLoginController::class, 'login'])
        ->name('agent.login');

    Route::view('/login-page', 'admin.login')
        ->name('admin.login.page');

    /* ---------- AREA TERPROTEKSI ---------- */
    Route::middleware('auth:admin')->group(function () {

        Route::view('/dashboard', 'admin.admin_dashboard')   // <-- diperbaiki
            ->name('admin.dashboard');

        /* Home Panel */
        Route::controller(HomeSectionController::class)->group(function () {
            Route::get('/banners', 'getBanners');
            Route::post('/create-banner', 'createBanner');
            Route::post('/update-banner/{id}', 'updateBanner');
            Route::delete('/delete-banner/{id}', 'deleteBanner');
        });

        Route::controller(ComingUpNextController::class)->group(function () {
            Route::get('/coming-up-next-content', 'getContent');
            Route::post('/create-coming-up-next-content', 'createContent');
            Route::put('/update-coming-up-next-content', 'updateContent');
        });

        Route::controller(VisionUpdateController::class)->group(function () {
            Route::get('/vision-update', 'getContent');
            Route::post('/create-vision-update', 'createContent');
            Route::post('/update-vision-update-image/{id}/{image_type}', 'updateImageContent');
            Route::put('/update-vision-update-text/{id}', 'updateTextContent');
            Route::delete('/delete-vision-update/{id}', 'deleteContent');
            Route::delete('/delete-vision-update-image/{id}/{image_type}', 'deleteImage');
        });

        Route::controller(EventsController::class)->group(function () {
            Route::get('/event', 'getEvent');
            Route::post('/create-event', 'createEvent');
            Route::post('/update-event', 'updateEvent');
        });

        Route::controller(AgentPageEditController::class)->group(function () {
            Route::get('/agent/{username}', 'getAgent');
            Route::put('/agent-edit/{username}', 'editAgent');
            Route::delete('/agent-delete/{username}', 'deleteAgent');
        });
    });
});