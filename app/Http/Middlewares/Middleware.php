<?php

namespace App\Http\Middlewares;

use Exception;

class Middleware{
    protected static array $middlewares = [
        'guest' => GuestMiddleware::class,
        'auth'  => AuthMiddleware::class,
    ];

    public static function resolve($middleware)
    {
        if(!array_key_exists($middleware,static::$middlewares)){
            throw new Exception("Middleware {$middleware} not exists.");
        }

        $class = new static::$middlewares[$middleware];
        $class->handle();
    }
}