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
        // The visitor's private demo database must be selected right after the session starts, before auth and
        // route model binding read anything (same lesson as the property demo, 18 Nov).
        $middleware->appendToPriorityList(\Illuminate\Session\Middleware\StartSession::class, \App\Http\Middleware\DemoSandbox::class);
        $middleware->web(append: [
            \App\Http\Middleware\DemoSandbox::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/billplz',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
