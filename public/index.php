<?php

use Slim\Factory\AppFactory;
use Middlewares\TrailingSlash;
use App\Handlers\ShutdownHandler;
use App\Handlers\HttpErrorHandler;
use App\Controllers\PostController;
use App\Middlewares\ReturningJsonMiddleware;
use App\Middlewares\JsonBodyParserMiddleware;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$displayErrorDetails = true;

$app = AppFactory::create();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Handling Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

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
