<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/product.php'));

            Route::middleware('web')
                ->group(base_path('routes/cart.php'));

            Route::middleware('web')
                ->group(base_path('routes/checkout.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withEvents(discover: [
        __DIR__ . '/../app/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
