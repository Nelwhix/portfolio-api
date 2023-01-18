<?php

namespace Nelwhix\PortfolioApi\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserHandler
{
    public function __construct(private Request $request, private Response $response){}


}