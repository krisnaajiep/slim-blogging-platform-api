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
}
