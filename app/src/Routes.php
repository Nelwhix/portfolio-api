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
        '/api/project',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'index']
    ],
    [
        'DELETE',
        '/api/project/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'destroy']
    ],
    [
        'GET',
        '/api/project/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'show']
    ],
    [
        'POST',
        '/api/project/{id}',
        ['Nelwhix\PortfolioApi\Handlers\ProjectHandler', 'update']
    ],
    [
        'POST',
        '/api/experience/create',
        ['Nelwhix\PortfolioApi\Handlers\ExperienceHandler', 'store']
    ]
];
