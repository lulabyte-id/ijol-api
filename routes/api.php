<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return ['version' => config('api.version')];
});

Route::middleware('auth:sanctum')->group( function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
});
