<?php

use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Http\Response;

$router->get('/users', function () {
    return new Response(['message' => 'Welcome to the users page!'], 200, ['Content-Type' => 'application/json']);
});

$router->get('/api/users', [UserController::class, 'index']);
$router->post('/api/users', [UserController::class, 'store']);
$router->get('/api/users/{id}', [UserController::class, 'show']);
$router->put('/api/users/{id}', [UserController::class, 'update']);
$router->delete('/api/users/{id}', [UserController::class, 'destroy']);

$router->get('/users/view', [UserController::class, 'showUsers']);
