<?php declare(strict_types=1);

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
        '/api/user/create',
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
        '/api/project/create',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'store']
    ],
    [
        'GET',
        '/api/projects',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'index']
    ]
];
