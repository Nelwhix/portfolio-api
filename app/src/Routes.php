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
    ]
];
