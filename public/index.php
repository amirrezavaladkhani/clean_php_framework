<?php

require_once __DIR__ . '/../bootstrap.php';

use Illuminate\Http\Request;

// دریافت و هندل درخواست
$request = Request::capture();
$response = $router->dispatch($request);

// ارسال پاسخ
$response->send();
