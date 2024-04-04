<?php

return [
    [
        'GET',
        '/',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'home']
    ],
    [
        'GET',
        '/ping',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'ping']
    ],
    [
        'POST',
        '/api/users',
        ['Nelwhix\PortfolioApi\Handlers\UserHandler', 'store']
    ],
    [
        'POST',
        '/api/user/login',
        ['Nelwhix\PortfolioApi\Handlers\UserHandler', 'login']
    ],
    [
        'GET',
        '/api/auth/refresh',
        ['Nelwhix\PortfolioApi\Handlers\UserHandler', 'refresh']
    ],
    [
        'POST',
        '/api/projects',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'store']
    ],
    [
        'GET',
        '/api/projects',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'index']
    ],
    [
        'DELETE',
        '/api/projects/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'destroy']
    ],
    [
        'GET',
        '/api/projects/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'show']
    ],
    [
        'POST',
        '/api/projects/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'update']
    ]
];
