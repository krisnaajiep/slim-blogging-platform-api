<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Models\Post;
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

    public function create(Request $request, Response $response, $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];

        $response = $response->withAddedHeader('Content-Type', 'application/json');

        $validation = Validator::setRules($data, [
            'title' => ['required', 'alpha_num_space', 'min_length:3', 'max_length:255'],
            'content' => ['required', 'max_length:65535'],
            'tags' => ['array_string']
        ]);

        if ($validation->hasValidationErrors()) {
            $response->getBody()->write(json_encode($validation->getValidationErrors()));
            return $response->withStatus(400);
        }


        $data['tags'] = json_encode($data['tags']);

        $post = $this->model->create($data);

        $post['tags'] = json_decode($post['tags']);

        $created_at = new \DateTime($post['created_at']);
        $updated_at = new \DateTime($post['updated_at']);

        $post['created_at'] = $created_at->format('Y-m-d\TH:i:s\Z');
        $post['updated_at'] = $updated_at->format('Y-m-d\TH:i:s\Z');

        $response->getBody()->write(json_encode($post));

        return $response->withStatus(201);
    }
}
