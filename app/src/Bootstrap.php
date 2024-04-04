<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi;

use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
use Treblle\Factory\TreblleFactory;

require __DIR__ . '/../vendor/autoload.php';
error_reporting(E_ALL);
ob_start();

// Load all the environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$treblle = TreblleFactory::create(
    $_ENV['TREBLLE_API_KEY'],
    $_ENV['TREBLLE_PROJECT_ID'],
    true
);

$whoops = new \Whoops\Run;

if ($_ENV['APP_ENV'] !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        echo '500: SERVER ERROR';
    });
}
$whoops->register();


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