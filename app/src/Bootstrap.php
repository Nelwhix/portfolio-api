<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi;

use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;

require __DIR__ . '/../vendor/autoload.php';

// Load all the environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$injector = include('Dependencies.php');

$request = $injector->make("Symfony\Component\HttpFoundation\Request");
$response = $injector->make("Symfony\Component\HttpFoundation\Response");
$response->headers->set('Content-Type', 'application/json');

$routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
    $routes = include('Routes.php');

    foreach($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
};

$dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        $response->setContent(json_encode([
            'message' => "Route not found"
        ]));
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent(json_encode([
            'message' => "Http method not allowed"
        ]));
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        break;
    case \FastRoute\Dispatcher::FOUND:
        $className = $routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2];

        $class = $injector->make($className);
        $class->$method($vars);
        break;
}

$response->send();