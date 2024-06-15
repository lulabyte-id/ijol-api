<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', HealthCheckResultsController::class);

require __DIR__.'/auth.php';
