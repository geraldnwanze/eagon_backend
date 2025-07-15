<?php

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Middleware\API\AdminMiddleware;
use App\Http\Middleware\API\ResidentMiddleware;
use App\Http\Middleware\API\SuperAdminMiddleware;
use App\Http\Middleware\API\TenantMiddleware;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            ForceJsonResponse::class
        ]);
        
        $middleware->alias([
            'super-admin' => SuperAdminMiddleware::class,
            'admin' => AdminMiddleware::class,
            'resident' => ResidentMiddleware::class,
            'tenant' => TenantMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(Throwable $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return ApiResponse::failure($exception->getMessage(), statusCode: 404);
            }
        });
    })->create();
