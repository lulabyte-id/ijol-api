<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

Route::get('/', HealthCheckJsonResultsController::class);

require __DIR__.'/auth.php';
