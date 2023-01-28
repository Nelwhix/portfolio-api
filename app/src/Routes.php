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
        'POST',
        '/api/project/create',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'store']
    ],
];
