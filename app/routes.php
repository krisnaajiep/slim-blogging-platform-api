<?php

use Slim\App;
use App\Controllers\PostController;

return function (App $app) {
    $app->get('/posts', PostController::class . ':index');
    $app->get('/posts/{id}', PostController::class . ':show');
    $app->post('/posts', PostController::class . ':create');
    $app->put('/posts/{id}', PostController::class . ':update');
    $app->delete('/posts/{id}', PostController::class . ':delete');
};
