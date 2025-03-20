<?php
namespace App\Http;

class Kernel
{
    protected $middlewares = [
        \App\Http\Middleware\NotFoundMiddleware::class,
    ];

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}
