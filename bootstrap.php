<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Http\Response;

require_once __DIR__ . '/vendor/autoload.php';

// 1. ایجاد Container و Event Dispatcher
$container = new Container();
$events = new Dispatcher($container);

// 2. ثبت CallableDispatcher در Container
$container->bind('Illuminate\Routing\Contracts\CallableDispatcher', fn($container) => new CallableDispatcher($container));

// 3. ایجاد Router
$router = new Router($events, $container);

// 4. تنظیم ORM (Eloquent)
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'erp_db',
    'username'  => 'project-access',
    'password'  => '*)74TLLtA5825ym*',
    'charset'   => 'utf8',
    'collation' => 'utf8_persian_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 5. تنظیم Blade Engine
$filesystem = new Filesystem();
$viewPaths = [__DIR__ . '/resources/views'];
$cachePath = __DIR__ . '/storage/cache';

// بررسی و ایجاد مسیر cache اگر وجود ندارد
if (!is_dir($cachePath)) {
    mkdir($cachePath, 0777, true);
}

// تنظیم Blade Compiler
$bladeCompiler = new BladeCompiler($filesystem, $cachePath);

// تنظیم EngineResolver
$resolver = new EngineResolver();
$resolver->register('blade', function () use ($bladeCompiler) {
    return new CompilerEngine($bladeCompiler);
});
$resolver->register('php', fn() => new PhpEngine());

// تنظیم FileViewFinder و Factory برای Viewها
$viewFinder = new FileViewFinder($filesystem, $viewPaths);
$viewFactory = new Factory($resolver, $viewFinder, $events);

// ثبت View Factory در Container
$container->instance('view', $viewFactory);

// 6. بارگذاری تمامی مسیرهای ماژول‌ها
foreach (glob(__DIR__ . '/app/Modules/*/routes.php') as $routeFile) {
    require_once $routeFile;
}

// 7. تعریف تابع response()
if (!function_exists('response')) {
    function response($content = '', $status = 200, $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

// 8. بارگذاری مسیرهای عمومی
require_once __DIR__ . '/routes/web.php';

// 9. ثبت Middlewareها در Container
$container->singleton('middleware', function ($container) {
    return [
        \App\Http\Middleware\NotFoundMiddleware::class,
    ];
});

// 10. اجرای Middlewareها
function runMiddlewares($middlewares, $request, $callback)
{
    $pipeline = array_reduce(
        array_reverse($middlewares),
        function ($next, $middleware) {
            return function ($request) use ($next, $middleware) {
                return (new $middleware())->handle($request, $next);
            };
        },
        $callback
    );
    return $pipeline($request);
}

// 11. تعریف توابع
if (!function_exists('view')) {
    function view($view, $data = [])
    {
        global $container;
        return $container->make('view')->make($view, $data)->render();
    }
}

if (!function_exists('asset')) {
    function asset($path = ""): string
    {
        return '../../assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url($path = ''): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return rtrim($scheme . $host, '/') . '/' . ltrim($path, '/');
    }
}



// 12. پردازش درخواست و ارسال پاسخ
$request = \Illuminate\Http\Request::capture();
$response = runMiddlewares(
    $container->make('middleware'),
    $request,
    fn($request) => $router->dispatch($request)
);

$response->send();
