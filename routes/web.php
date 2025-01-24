<?php
use Illuminate\Routing\Router;

$router->get('/', function () {
    return view('welcome', ['title' => 'My ERP']);
});

$router->get('/api/example', function () {
    return response()->json(['message' => 'API Working']);
});
