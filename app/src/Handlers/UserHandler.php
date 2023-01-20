<?php

namespace Nelwhix\PortfolioApi\Handlers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;

class UserHandler
{
    public function __construct(private Request $request, private Response $response){}

    public function store() {
        // validate request parameters
        $email = $this->request->request->get('email');

        if (!$email) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Email field is required"
            ]));

            return;
        }
        $name = $this->request->request->get('name');

        if (!$name) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Name field is required"
            ]));

            return;
        }
        $password = $this->request->request->get('password');

        if (!$password) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Password field is required"
            ]));

            return;
        }
        $password_confirmation = $this->request->request->get('password_confirmation');


        if (!v::email()->validate($email)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Please input a valid email"
            ]));

            return;
        }

        if($password != $password_confirmation) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Your password and confirmation must match"
            ]));
            return;
        }

    }
}