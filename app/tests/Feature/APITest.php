<?php

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

beforeAll(function () {
    $pid = pcntl_fork();

    if ($pid == -1) {
        die('could not fork');
    } else if ($pid) {
        pcntl_wait($status);
    } else {
        shell_exec("php -S localhost:8888");
    }
});

it('starts', function () {
    $client = new Client([
        'base_uri' => 'http://localhost:8888',
        'timeout' => 2.0
    ]);

    $response = $client->get('/');

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);
});
