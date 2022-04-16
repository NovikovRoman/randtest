<?php
/**
 * Example
 */
require_once __DIR__ . '/vendor/autoload.php';

use Response\Response;
use Router\Route;
use Router\Router;

$routes = [
    new Route(
        ['GET'],
        '/generate/',
        'Controllers\GenerateController::index'
    ),
];

$router = new Router();

foreach ($routes as $route) {
    $router->addRoute($route);
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$pURI = parse_url($_SERVER['REQUEST_URI']);

try {
    $route = $router->match($_SERVER['REQUEST_METHOD'], $pURI['path']);

} catch (Exception $e) {
    (new Response('Page not found', Response::HTTP_NOT_FOUND))->out();
    die;
}

if (!str_contains($route->getController(), '::')) {
    (new Response('bad controller', Response::HTTP_SERVER_ERROR))->out();
    die;
}

list($className, $classMethod) = explode('::', $route->getController());

if (!class_exists($className)) {
    (new Response(
        $className . ' class not found',
        Response::HTTP_SERVER_ERROR)
    )->out();
    die;
}

$class = new $className();
if (!method_exists($class, $classMethod)) {
    (new Response(
        $classMethod . ' method of ' . $className . ' class ' . $classMethod . ' not found',
        Response::HTTP_SERVER_ERROR)
    )->out();
    die;
}

try {
    $res = (new $class())->$classMethod();
    if ($res instanceof Response) {
        $res->out();
    }

} catch (Exception $e) {
    (new Response($e->getMessage(), Response::HTTP_SERVER_ERROR))->out();
    die;
}
