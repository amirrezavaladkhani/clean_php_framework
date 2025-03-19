<?php
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

$router->get('/', function () {
//    return response('<h1>Welcome to My ERP</h1>', 200, ['Content-Type' => 'text/html']);
//    return \Illuminate\Support\Facades\View::
    return view('pages.dashboard');
});


$router->get('/api/example', function () {
    return response(['message' => 'API Working'], 200, ['Content-Type' => 'application/json']);
});

$router->get('/test', function () {
    var_dump('hello developer!');
});


