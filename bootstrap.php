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
require_once __DIR__ . '/app/Support/helpers.php';

// 1. Create Application Container & Event Dispatcher
$container = new Container();
$events = new Dispatcher($container);

// 2. Register CallableDispatcher
$container->bind(
    'Illuminate\Routing\Contracts\CallableDispatcher',
    fn($container) => new CallableDispatcher($container)
);

// 3. Initialize Router
$router = new Router($events, $container);

// 4. Setup Eloquent ORM (Database)
$capsule = new Capsule();
$capsule->addConnection(config('database.connections.mysql'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 5. Setup Blade View Engine
$filesystem = new Filesystem();
$viewPaths = config('view.paths');
$cachePath = config('view.cache');

// Ensure cache directory exists
if (!is_dir($cachePath)) {
    mkdir($cachePath, 0777, true);
}

// Configure Blade Compiler
$bladeCompiler = new BladeCompiler($filesystem, $cachePath);

// Setup Engine Resolver for Blade & PHP Views
$resolver = new EngineResolver();
$resolver->register('blade', fn() => new CompilerEngine($bladeCompiler));
$resolver->register('php', fn() => new PhpEngine());

// Configure View Factory
$viewFinder = new FileViewFinder($filesystem, $viewPaths);
$viewFactory = new Factory($resolver, $viewFinder, $events);

// Register View Factory in Container
$container->instance('view', $viewFactory);

// 6. Load Module Routes Dynamically
$modules = config('modules.modules') ?? [];
foreach ($modules as $module) {
    $routeFile = __DIR__ . "/app/Modules/{$module}/routes.php";
    if (file_exists($routeFile)) {
        require_once $routeFile;
    }
}

// 7. Define Global Response Function
if (!function_exists('response')) {
    function response($content = '', $status = 200, $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

// 8. Load General Routes
$webRoutes = __DIR__ . '/routes/web.php';
if (file_exists($webRoutes)) {
    require_once $webRoutes;
}

// 9. Register Middleware
$kernel = new \App\Http\Kernel();
$container->singleton('middleware', fn($container) => $kernel->getMiddlewares());

// 10. Middleware Execution Pipeline
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

// 11. Define Utility Functions
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

// 12. Handle Incoming Request & Send Response
$request = \Illuminate\Http\Request::capture();
$response = runMiddlewares(
    $container->make('middleware'),
    $request,
    fn($request) => $router->dispatch($request)
);

$response->send();
