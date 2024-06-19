<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
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
            \Illuminate\Session\Middleware\StartSession::class
        ]);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (!request()->is('admin*')) {
            // Handle exceptions to be in JSON format
            $exceptions->render(function (Exception $exception) {
                $response = [
                    'message' => $exception->getMessage(),
                ];

                if (app()->hasDebugModeEnabled()) {
                    $response['trace'] = $exception->getTrace();
                }

                if (request()->is('api/*')) {
                    if ($exception instanceof AuthenticationException) {
                        return response()->json([
                            'message' => 'Unauthorized'
                        ], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
                    }

                    if ($exception instanceof RuntimeException) {
                        return response()->json([
                            'message' => $exception->getMessage()
                        ], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    if ($exception instanceof \Laravel\Socialite\Two\InvalidStateException) {
                        return response()->json([
                           'message' => $exception->getMessage()
                        ], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }

                return response()->json($response, $exception->getStatusCode());
            });
        };

        // Handle exceptions with Sentry
        Integration::handles($exceptions);
    })
    ->create();
