<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectHandler
{
    public function __construct(private Request $request, private Response $response){}

    public function ping() {
        $this->response->setStatusCode(200);

        $this->response->setContent(json_encode([
            'message' => 'pong @' . \Carbon\Carbon::now() . " UTC"
        ]));
    }
}