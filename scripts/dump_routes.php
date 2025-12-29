<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool)($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();
$router = $kernel->getContainer()->get('router');
$routes = $router->getRouteCollection();

foreach ($routes as $name => $route) {
    echo $name . ' => ' . $route->getPath() . "\n";
}
