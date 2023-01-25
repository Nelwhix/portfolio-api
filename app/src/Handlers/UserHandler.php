<?php

namespace Nelwhix\PortfolioApi\Handlers;

use Carbon\CarbonImmutable;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nelwhix\PortfolioApi\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;

class UserHandler
{
    public function __construct(private Request $request, private Response $response){}
    
    public function store() {
        // validate request parameter
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

        // check whether that email has been used before
        $result = $collection->findOne(
            ['email' => $email],
            ["projection" => [
                "email" => 1
            ]]
        );

        if ($result?->email) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                'message' => "Email taken"
            ]));

            return;
        }


        $result = $collection->insertOne([
           'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);



        $token = $this->generateToken($name);

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

        $database = new Database();

        $collection = $database->database->users;

        // check for user
        $result = $collection->findOne(
            ['email' => $email],
            ["projection" => [
                "name" => 1,
                "email" => 1,
                "password" => 1
            ]]
        );

        if (!$result?->email) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Email/Password does not exist"
            ]));

            return;
        }

        if (!password_verify($password, $result->password)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Email/Password does not exist"
            ]));

            return;
        }

        $token = $this->generateToken($result->name);

        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent(json_encode([
            "message" => "Login successful",
            "token" => $token
        ]));
    }

    private function generateToken(String $name): string {
        $secret_key = $_ENV['JWT_SECRET'];
        $now = new CarbonImmutable();
        $expire_at = $now->addMinutes(3)->getTimestamp();
        $domainName = $_ENV['API_URL'];
        $request_data = [
            'iat' => $now->getTimestamp(),
            'iss' => $domainName,
            'nbf' => $now->getTimestamp(),
            'exp' => $expire_at,
            'userName' => $name
        ];

        return JWT::encode($request_data, $secret_key, 'HS512');
    }
}