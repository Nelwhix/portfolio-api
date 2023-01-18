<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectHandler
{
    public function __construct(private Request $request, private Response $response){}

    public function ping() {
        $this->response->setStatusCode(Response::HTTP_OK);

        $this->response->setContent(json_encode([
            'message' => 'pong @' . \Carbon\Carbon::now() . " UTC"
        ]));
    }

    public function home() {
        $this->response->setStatusCode(Response::HTTP_OK);

        $this->response->setContent(json_encode([
            'message' => 'Portfolio API V1 by Nelson Isioma'
        ]));
    }
}