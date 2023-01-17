<?php declare(strict_types=1);

$injector = new \Auryn\Injector;

$injector->share('Symfony\Component\HttpFoundation\Request');

$injector->define('Symfony\Component\HttpFoundation\Request', [
        ':query' => $_GET,
        ':request' => $_POST,
        ':cookies' => $_COOKIE,
        ':files' => $_FILES,
        ':server' => $_SERVER
    ]);

$injector->share('Symfony\Component\HttpFoundation\Response');

return $injector;
