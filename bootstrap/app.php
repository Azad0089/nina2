<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/donations/nowpayments/ipn',
            'api/donations/nowpayments/*',
            'api/donations/bitcart/ipn',
            'api/nowpayments/ipn',
            'admin/smtp/test',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
