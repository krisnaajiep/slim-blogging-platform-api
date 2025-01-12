<?php

use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;
use App\Controllers\PostController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\JsonBodyParserMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true)
	->getDefaultErrorHandler()
	->forceContentType('application/json');

$app->add(new TrailingSlash(trailingSlash: false));

$app->add(new JsonBodyParserMiddleware());

$app->get('/', function (Request $request, Response $response, $args) {
	$response->getBody()->write('Hello World');
	return $response;
});

$app->post('/posts', [PostController::class, 'create']);
$app->get('/posts/{id}', [PostController::class, 'show']);

$app->run();
