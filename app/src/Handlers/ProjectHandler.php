<?php declare(strict_types=1);

namespace Nelwhix\PortfolioApi\Handlers;

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\InvalidArgumentException;
use Nelwhix\PortfolioApi\Database;
use Nelwhix\PortfolioApi\Helpers;
use Nelwhix\PortfolioApi\Validator;
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

        $name = $this->request->query->get('name');

        if ($name === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Name field is required"
            ]));

            return;
        }

        if (!Validator::string($name)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Name field is not valid string"
            ]));

            return;
        }
        $description = $this->request->query->get('description');


        if ($description === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Description field is required"
            ]));

            return;
        }

        if (!Validator::string($description)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Description field is not valid string"
            ]));

            return;
        }
        $tools = $this->request->query->get('tools');


        if ($tools === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tools field is required"
            ]));

            return;
        }

        if (!Validator::string($tools)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tools field is not valid string"
            ]));

            return;
        }
        $githubLink = $this->request->query->get('githubLink');


        if ($githubLink === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Github link is required"
            ]));

            return;
        }

        if (!Validator::string($githubLink)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "githubLink field is required"
            ]));

            return;
        }

        if (!Validator::url($githubLink)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Please enter a valid url"
            ]));

            return;
        }
        $projectLink = $this->request->query->get('projectLink');


        if ($projectLink === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Project link is required"
            ]));

            return;
        }

        if (!Validator::string($projectLink)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "projectLink field is required"
            ]));

            return;
        }

        if (!Validator::url($projectLink)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Please enter a valid url"
            ]));

            return;
        }

        $tag = $this->request->query->get('tag');


        if ($tag === null) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tag field is required"
            ]));

            return;
        }

        if (!Validator::string($tag)) {
            $this->response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->response->setContent(json_encode([
                "message" => "Tag field is required"
            ]));

            return;
        }

        $database = new Database();

        $collection = $database->database->projects;

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

        $count = 0;
        foreach($projects as $project) {
            $count++;
            $result[] = $project;
        }


        $this->response->setStatusCode(Response::HTTP_OK);
        if (isset($result)) {
            $this->response->setContent(json_encode([
                'message' => 'successful',
                'total' => $count,
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

        try {
            $_id = new ObjectId($vars['id']);
        } catch (InvalidArgumentException $err) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->setContent(json_encode([
                "message"=> "Invalid Object ID"
            ]));

            return;
        }

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

         try {
            $_id = new ObjectId($vars['id']);
        } catch (InvalidArgumentException $err) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->setContent(json_encode([
                "message"=> "Invalid Object ID"
            ]));

            return;
        }

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

    public function update(array $vars) {
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
        $_id = new ObjectId($vars["id"]);

        $project = $collection->findOne([ "_id" => $_id ]);

        $name = $this->request->query->get('name') ?: $project->name;
        $description = $this->request->query->get('description') ?: $project->description;
        $tools = $this->request->query->get('tools') ?: $project->tools;
        $githubLink = $this->request->query->get('githubLink') ?: $project->githubLink;
        $projectLink = $this->request->query->get('projectLink') ?: $project->projectLink;
        $tag = $this->request->query->get('tag') ?: $project->tag;

        $result = $collection->updateOne(
            ["_id" => $_id],
            ['$set' => [
                'name' => $name,
                'description' => $description,
                'tools' => $tools,
                'githubLink' => $githubLink,
                'projectLink' => $projectLink,
                'tag' => $tag
            ]],
        );

        if ($result->getMatchedCount() != 1) {
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
            $this->response->setContent(json_encode([
                'message' => 'Resource not found'
            ]));

            return;
        }

        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent(json_encode([
            'message' => 'successful ' . $result->getModifiedCount()
        ]));
    }
}