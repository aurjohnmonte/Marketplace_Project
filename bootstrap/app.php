<?php

use App\Http\Middleware\BuyerCheck;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\UserCheckMiddleware;
use App\Http\Middleware\UserloginMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                "userlogin" => UserloginMiddleware::class,
                "usercheck" => UserCheckMiddleware::class,
                "buyercheck" => BuyerCheck::class,
                'admin_check' => CheckAdmin::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
