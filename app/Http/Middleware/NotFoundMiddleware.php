<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;

class NotFoundMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse([
                'error' => 'Route not found',
                'status' => 404,
            ], 404);
        }
    }
}
