<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\OauthController;
use App\Services\TokenService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['version' => config('api.version')];
});

Route::post('/register', [ApiAuthController::class, 'register'])->name('register');
Route::post('/login', [ApiAuthController::class, 'login'])->name('login');

Route::get('/oauth/{provider}/callback', [OauthController::class, 'handleProviderCallback']);
Route::get('/oauth/{provider}/deletion', [OauthController::class, 'handleDeletion']);
// Caution! Following route shall be used for testing purposes only
Route::get('/oauth/{provider}', [OauthController::class, 'redirectToProvider']);

/*
 * PROTECTED ROUTES
 */

Route::middleware(['auth:sanctum', 'ability:' . TokenService::GetAccessApiAbility()])
    ->group(function () {
        Route::get('/logout', [ApiAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [ApiAuthController::class, 'me'])->name('me');
    });

Route::middleware(['auth:sanctum', 'ability:' . TokenService::GetIssueTokenAbility()])
    ->group(function () {
        Route::get('/refresh', [ApiAuthController::class, 'refresh'])->name('refresh');
    });
