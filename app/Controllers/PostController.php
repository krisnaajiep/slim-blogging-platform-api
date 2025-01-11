<?php

namespace App\Controllers;

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
        $data = $request->getParsedBody();

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
