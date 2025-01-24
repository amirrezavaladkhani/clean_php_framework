<?php
require_once __DIR__ . '/../bootstrap.php';

$router->dispatch(
    Illuminate\Http\Request::capture()
);
