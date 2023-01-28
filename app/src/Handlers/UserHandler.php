<?php

namespace Nelwhix\PortfolioApi\Handlers;

use Carbon\CarbonImmutable;
use Faker\Factory;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nelwhix\PortfolioApi\Database;
use Nelwhix\PortfolioApi\Helpers;
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

        // check if email is unique
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

        $user = $collection->findOne(
            ['_id' => $result->getInsertedId()],
        );

        $this->response->setStatusCode(Response::HTTP_CREATED);

        $this->response->setContent(json_encode([
            "message" => "User created successfully",
            "data" => [
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

        $faker = Factory::create();

        if (!$result?->email) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Login failed from " . $faker->ipv4()
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

        $tokenArray = $this->generateToken($result->name);

        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent(json_encode([
            "message" => "Login successful",
            "tokens" => $tokenArray
        ]));
    }

    public function refresh() {
        $token = explode(" ", $this->request->headers->get("Authorization"))[1];

        $secret_Key  = $_ENV['JWT_SECRET'];

        try {
            $token = JWT::decode($token, new Key($secret_Key, 'HS512'));
        } catch (\Exception) {
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->response->setContent(json_encode([
                "message" => "Hello hacker, have a nice day!"
            ]));

            return;
        }

        $now = new CarbonImmutable();
        $serverName = $_ENV['API_URL'];

        if ($token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->response->setContent(json_encode([
                "message" => "Hello hacker, have a nice day!"
            ]));
        }

        dd($token);

        $tokenArray = $this->generateToken($token->name);

        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent(json_encode([
            "message" => "tokens refreshed",
            "tokens" => $tokenArray
        ]));

    }

    private function generateToken(String $name): array {
        $secret_key = $_ENV['JWT_SECRET'];
        $now = new CarbonImmutable();

        // access token expire time
        $expire_at1 = $now->addMinutes(3)->getTimestamp();
        $domainName = $_ENV['API_URL'];
        $request_data1 = [
            'iat' => $now->getTimestamp(),
            'iss' => $domainName,
            'nbf' => $now->getTimestamp(),
            'exp' => $expire_at1,
            'userName' => $name
        ];

        // refresh token expire time
        $expire_at2 = $now->addMinutes(15)->getTimestamp();
        $request_data2 = [
            'iat' => $now->getTimestamp(),
            'iss' => $domainName,
            'nbf' => $now->getTimestamp(),
            'exp' => $expire_at2,
            'userName' => $name
        ];

        return [
            JWT::encode($request_data1, $secret_key, 'HS512'),
            JWT::encode($request_data2, $secret_key, 'HS512')
        ];
    }
}