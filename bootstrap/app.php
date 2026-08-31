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
       'locale' => \App\Http\Middleware\SetLocale::class,
       'checkOut-checker' => \App\Http\Middleware\CheckoutMiddleWare::class,
       'secure' => \App\Http\Middleware\SecureTokenMiddleware::class,
       'security.event.logger' => \App\Http\Middleware\SecurityEventLogger::class,
      ]);

      $middleware->prependToGroup('web', \App\Http\Middleware\SecurityEventLogger::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])->create();
