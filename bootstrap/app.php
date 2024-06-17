<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle exceptions to be in JSON format
        $exceptions->render(function (Exception $exception) {
            if (request()->is('admin*')) {
                return;
            };

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 401);
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        });

        // Sentry integration
        Integration::handles($exceptions);
    })
    ->create();
