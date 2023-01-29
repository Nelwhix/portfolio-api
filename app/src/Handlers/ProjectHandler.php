<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi\Handlers;

use MongoDB\BSON\ObjectId;
use Nelwhix\PortfolioApi\Database;
use Nelwhix\PortfolioApi\Helpers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Ulid;

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
            'message' => 'Portfolio API '.  $_ENV['API_VERSION'] . ' by Nelson Isioma'
        ]));
    }

    public function store() {
        if (!$this->request->headers->get("Authorization")) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->setContent(json_encode([
                "message" => "Action authorization error"
            ]));

            return;
        }

        $token = explode(" ", $this->request->headers->get("Authorization"))[1];

        $isAuthorized = Helpers::isAuthorized($token);

        if (!$isAuthorized) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->setContent(json_encode([
                "message"=> "Action authorization error"
            ]));

            return;
        }

        $name = $this->request->request->get('name');

        if (!$name) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Name field is required"
            ]));

            return;
        }
        $description = $this->request->request->get('description');

        if (!$description) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Description field is required"
            ]));

            return;
        }
        $tools = $this->request->request->get('tools');

        if (!$tools) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tools field is required"
            ]));

            return;
        }
        $githubLink = $this->request->request->get('githubLink');

        if (!$githubLink) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "githubLink field is required"
            ]));

            return;
        }
        $projectLink = $this->request->request->get('projectLink');

        if (!$projectLink) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "projectLink field is required"
            ]));

            return;
        }
        $tag = $this->request->request->get('tag');

        if (!$tag) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tag field is required"
            ]));

            return;
        }

        $database = new Database();

        $collection = $database->database->projects;

        $plid = new Ulid();
        $result = $collection->insertOne([
            'name' => $name,
            'description' => $description,
            'githubLink' => $githubLink,
            'projectLink' => $projectLink,
            'tools' => $tools,
            'tag' => $tag,
        ]);

        $project = $collection->findOne(
            ['_id' => $result->getInsertedId()],
        );

        $this->response->setStatusCode(Response::HTTP_CREATED);

        $this->response->setContent(json_encode([
            "message" => "Project added successfully",
            "data" => [
                "project" => $project
            ]
        ]));

    }

    public function index() {
        $conn = new Database();
        $collection = $conn->database->projects;

        $projects = $collection->find();

        foreach($projects as $project) {
            $result[] = $project;
        }


        $this->response->setStatusCode(Response::HTTP_OK);
        if (isset($result)) {
            $this->response->setContent(json_encode([
                'message' => 'successful',
                'projects' => $result,
            ]));

            return;
        }

        $this->response->setContent(json_encode([
            'message' => 'You don\'t have any projects',
        ]));
    }

    public function destroy(Array $vars) {
        if (!$this->request->headers->get("Authorization")) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->setContent(json_encode([
                "message" => "Action authorization error"
            ]));

            return;
        }

        $token = explode(" ", $this->request->headers->get("Authorization"))[1];

        $isAuthorized = Helpers::isAuthorized($token);

        if (!$isAuthorized) {
            $this->response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $this->response->setContent(json_encode([
                "message"=> "Action authorization error"
            ]));

            return;
        }

        $conn = new Database();
        $collection = $conn->database->projects;

        $_id = new ObjectId($vars['id']);
        $result = $collection->deleteOne([
            "_id" => $_id
        ]);

        if ($result->getDeletedCount() == 0) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->setContent(json_encode([
                'message' => 'Project with id:' . $vars['id'] . ' not found'
            ]));

            return;
        }

        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent(json_encode([
            'message' => 'successful, ' . $result->getDeletedCount()
        ]));
    }

    public function show(array $vars) {
        $conn = new Database();
        $collection = $conn->database->projects;

        $_id = new ObjectId($vars['id']);
        $result = $collection->findOne([
            "_id" => $_id
        ]);

        if (!isset($result)) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->setContent(json_encode([
                'message' => "Project with id:" . $vars["id"] . ' not found'
            ]));

            return;
        }

        $this->response->setStatusCode(200);
        $this->response->setContent(json_encode([
            'message' => 'successful',
            'project' => $result
        ]));
    }
}