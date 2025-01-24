<?php

use Illuminate\Routing\Router;
use App\Modules\User\Controllers\UserController;

$router->get('/users', function () {
    return response()->json(['message' => 'User module works!']);
});

$router->get('/api/users', [UserController::class, 'index']);
$router->post('/api/users', [UserController::class, 'store']);
$router->get('/api/users/{id}', [UserController::class, 'show']);
$router->put('/api/users/{id}', [UserController::class, 'update']);
$router->delete('/api/users/{id}', [UserController::class, 'destroy']);

$router->get('/users/view', [UserController::class, 'showUsers']);
