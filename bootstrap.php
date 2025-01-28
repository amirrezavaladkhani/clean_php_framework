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
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Http\Response;

require_once __DIR__ . '/vendor/autoload.php';


// 1. Create the main Container
$container = new Container();

// 2. Register CallableDispatcher in the Container
$container->bind('Illuminate\Routing\Contracts\CallableDispatcher', fn($container) => new CallableDispatcher($container));

// 3. Create Event Dispatcher and Router
$events = new Dispatcher($container);
$router = new Router($events, $container);

// 4. Configure ORM (Eloquent)
$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'erp_db',
    'username' => 'project-access',
    'password' => '*)74TLLtA5825ym*',
    'charset' => 'utf8',
    'collation' => 'utf8_persian_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();


// 5. Configure View Engine
$filesystem = new Filesystem(); // For handling file operations
$viewPaths = [__DIR__ . '/app/Views']; // Define paths for view files
$cachePath = __DIR__ . '/storage/cache'; // Define path for cache files

// Register the PHP engine for rendering views
$resolver = new EngineResolver();
$resolver->register('php', fn() => new PhpEngine());

// Configure view finder and factory
$viewFinder = new FileViewFinder($filesystem, $viewPaths);
$viewFactory = new Factory($resolver, $viewFinder, $events);

// Register the view factory in the Container
$container->instance('view', $viewFactory);

// 6. Load all module routes dynamically
foreach (glob(__DIR__ . '/app/Modules/*/routes.php') as $routeFile) {
    require_once $routeFile;
}

// 7. Define the "response" helper function
if (!function_exists('response')) {
    function response($content = '', $status = 200, $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}

// 8. Load global routes
require_once __DIR__ . '/routes/web.php';

// 9. Register Middleware
$container->singleton('middleware', function ($container) {
    return [
        \App\Http\Middleware\NotFoundMiddleware::class,
    ];
});


// 10. Middleware Runner Function
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

// 11. Process request and handle response
$request = \Illuminate\Http\Request::capture();
$response = runMiddlewares(
    $container->make('middleware'),
    $request,
    fn($request) => $router->dispatch($request)
);

// Send the response
$response->send();
