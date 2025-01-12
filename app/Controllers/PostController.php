<?php

namespace App\Controllers;

use App\Models\Post;
use App\Validators\PostValidator;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PostController
{
    private $model;

    public function __construct()
    {
        $this->model = new Post();
    }

    public function index(Request $request, Response $response, $args): ResponseInterface
    {
        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $posts = $this->model->getAll();

        foreach ($posts as $key => $post) {
            $posts[$key] = $this->formatPost($post);
        }

        $response->getBody()->write(json_encode($posts));
        return $response;
    }

    public function create(Request $request, Response $response, $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $validation = PostValidator::validate($data);
        if ($validation->hasValidationErrors()) {
            $response->getBody()->write(json_encode($validation->getValidationErrors()));
            return $response->withStatus(400);
        }

        $data['tags'] = json_encode($data['tags']);
        $post = $this->model->create($data);

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response->withStatus(201);
    }

    public function show(Request $request, Response $response, $args): ResponseInterface
    {
        $id = $args['id'];

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $post = $this->model->getById($id);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response;
    }

    public function update(Request $request, Response $response, $args): ResponseInterface
    {
        $id = $args['id'];
        $data = $request->getParsedBody() ?? [];

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $validation = PostValidator::validate($data);
        if ($validation->hasValidationErrors()) {
            $response->getBody()->write(json_encode($validation->getValidationErrors()));
            return $response->withStatus(400);
        }

        $data['tags'] = json_encode($data['tags']);
        $post = $this->model->update($id, $data);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response;
    }

    public function delete(Request $request, Response $response, $args): ResponseInterface
    {
        $id = $args['id'];

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $post = $this->model->getById($id);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $this->model->delete($id);

        $response->getBody()->write(json_encode(['message' => 'Post deleted']));
        return $response->withStatus(204);
    }

    private function formatPost($post)
    {
        $post['tags'] = json_decode($post['tags']);

        $created_at = new \DateTime($post['created_at']);
        $updated_at = new \DateTime($post['updated_at']);

        $post['created_at'] = $created_at->format('Y-m-d\TH:i:s\Z');
        $post['updated_at'] = $updated_at->format('Y-m-d\TH:i:s\Z');

        return $post;
    }
}
