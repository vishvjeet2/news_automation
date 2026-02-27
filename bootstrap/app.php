<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function ($middleware) {
        $middleware->alias([
        'auth.check' => \App\Http\Middleware\AuthCheck::class,
    ]);
    })

    ->withExceptions(function ($exceptions) {
        //
    })

    ->create();