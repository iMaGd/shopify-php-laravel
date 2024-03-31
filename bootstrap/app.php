<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except:[
            'shopify_session_id',
            'shopify_session_id_sig'
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LogRequest::class,
            \App\Http\Middleware\EnsureShopifyInstalled::class,
            \App\Http\Middleware\AttemptShopifyAuthentication::class,
            \App\Http\Middleware\SetContentSecurityPolicy::class
        ]);

        $middleware->alias([
            'logRequest' => \App\Http\Middleware\LogRequest::class,
            'shopify.installed' => \App\Http\Middleware\EnsureShopifyInstalled::class,
            'shopify.auth' => \App\Http\Middleware\AttemptShopifyAuthentication::class,
            'shopify.csp' => \App\Http\Middleware\SetContentSecurityPolicy::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class
        ]);

        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
