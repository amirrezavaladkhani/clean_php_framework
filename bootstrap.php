<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Routing\Router;
use Illuminate\Container\Container;

require_once __DIR__ . '/vendor/autoload.php';

// Container
$container = new Container;
$router = new Router($container);

// ORM (Eloquent)
$capsule = new Capsule;
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

// View Engine (Blade)
$viewPaths = [__DIR__ . '/app/Views'];
$cachePath = __DIR__ . '/storage/cache';
$viewFactory = new Illuminate\View\Factory(
    new Illuminate\View\Engines\EngineResolver,
    new Illuminate\View\FileViewFinder($container, $viewPaths),
    $container
);

//  Container
$container->instance('view', $viewFactory);

// module routers
foreach (glob(__DIR__ . '/app/Modules/*/routes.php') as $routeFile) {
    require $routeFile;
}

