<?php

namespace Nelwhix\PortfolioApi\Handlers;

use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Nelwhix\PortfolioApi\Database;
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

        $database = new Database();

        $collection = $database->database->users;
        $result = $collection->insertOne([
           'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $secret_key = '68V0zWFrS72GbpPreidkQFLfj4v9m3Ti+DXc8OB0gcM=';
        $date = new CarbonImmutable();
        $expire_at = $date->addMinutes(6)->getTimestamp();
        $domainName = "127.0.0.1:8088";
        $request_data = [
            'iat' => $date->getTimestamp(),
            'iss' => $domainName,
            'nbf' => $date->getTimestamp(),
            'exp' => $expire_at,
            'userName' => $name
        ];

        $token = JWT::encode($request_data, $secret_key, 'HS512');

        $user = $collection->findOne(['_id' => $result->getInsertedId()]);

        $this->response->setStatusCode(Response::HTTP_CREATED);

        $this->response->setContent(json_encode([
            "message" => "User created successfully",
            "data" => [
                "token" => $token,
                "user" => $user
            ]
        ]));
    }

    public function login() {
        $email = $this->request->request->get('email');

        if (!$email) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Email field is required"
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
    }
}