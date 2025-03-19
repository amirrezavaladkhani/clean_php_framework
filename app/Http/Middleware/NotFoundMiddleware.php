<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotFoundMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (NotFoundHttpException $e) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'error' => 'Route not found',
                    'status' => 404,
                ], 404);
            }

            return new Response(view('errors.404'), 404);
        }
    }
}
