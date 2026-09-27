<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HomeSectionController,
    ComingUpNextController,
    VisionUpdateController,
    EventsController,
    AgentPageEditController,
    AgentLoginController
};


/*
|--------------------------------------------------------------------------
| AGENT LOGIN
|--------------------------------------------------------------------------
| Untuk sementara kita biarkan Agent di API.
| Nanti saat mengerjakan Agent, kita bisa pindahkan ke /agent-login.
|--------------------------------------------------------------------------
*/

Route::middleware('web')->prefix('visionasurance')->group(function () {

    Route::match(['get', 'post'], '/agent-login', [AgentLoginController::class, 'login'])
        ->name('agent.login');

});


/*
|--------------------------------------------------------------------------
| ADMIN API
|--------------------------------------------------------------------------
| Endpoint data yang dipakai Dashboard Admin.
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth:admin'])
    ->prefix('visionasurance')
    ->group(function () {

        /* Home Panel */

        Route::controller(HomeSectionController::class)->group(function () {

            Route::get('/banners', 'getBanners');

            Route::post('/create-banner', 'createBanner');

            Route::post('/update-banner/{id}', 'updateBanner');

            Route::delete('/delete-banner/{id}', 'deleteBanner');

        });


        /* Coming Up Next */

        Route::controller(ComingUpNextController::class)->group(function () {

            Route::get('/coming-up-next-content', 'getContent');

            Route::post('/create-coming-up-next-content', 'createContent');

            Route::put('/update-coming-up-next-content', 'updateContent');

        });


        /* Vision Update */

        Route::controller(VisionUpdateController::class)->group(function () {

            Route::get('/vision-update', 'getContent');

            Route::post('/create-vision-update', 'createContent');

            Route::post(
                '/update-vision-update-image/{id}/{image_type}',
                'updateImageContent'
            );

            Route::put(
                '/update-vision-update-text/{id}',
                'updateTextContent'
            );

            Route::delete(
                '/delete-vision-update/{id}',
                'deleteContent'
            );

            Route::delete(
                '/delete-vision-update-image/{id}/{image_type}',
                'deleteImage'
            );

        });


        /* Event */

        Route::controller(EventsController::class)->group(function () {

            Route::get('/event', 'getEvent');

            Route::post('/create-event', 'createEvent');

            Route::post('/update-event', 'updateEvent');

        });


        /* Agent Management */

        Route::controller(AgentPageEditController::class)->group(function () {

            Route::get('/agent/{username}', 'getAgent');

            Route::put('/agent-edit/{username}', 'editAgent');

            Route::delete('/agent-delete/{username}', 'deleteAgent');

        });

    });