<?php

use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;
use App\Controllers\PostController;
use App\Middlewares\JsonBodyParserMiddleware;
use App\Middlewares\ReturningJsonMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true)
	->getDefaultErrorHandler()
	->forceContentType('application/json');

// Middlewares
$app->add(new TrailingSlash(trailingSlash: false));
$app->add(new JsonBodyParserMiddleware());
$app->add(new ReturningJsonMiddleware());

// Routes
$app->post('/posts', [PostController::class, 'create']);
$app->get('/posts/{id}', [PostController::class, 'show']);
$app->get('/posts', [PostController::class, 'index']);
$app->put('/posts/{id}', [PostController::class, 'update']);
$app->delete('/posts/{id}', [PostController::class, 'delete']);

$app->run();
