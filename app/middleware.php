<?php

use Slim\App;
use Middlewares\TrailingSlash;
use App\Middlewares\TrimInputMiddleware;
use App\Middlewares\ReturningJsonMiddleware;

return function (App $app) {
    $app->add(new TrailingSlash(trailingSlash: false));
    $app->add(new TrimInputMiddleware());
    $app->add(new ReturningJsonMiddleware());

    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();
};
