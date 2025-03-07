<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    protected $middleware = [
        \App\Http\Middleware\ResponseMiddleware::class,
        // other global middlewares...
    ];
    protected $routeMiddleware = [
        'response' => \App\Http\Middleware\ResponseMiddleware::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,

    ];
}
