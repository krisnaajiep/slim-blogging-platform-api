<?php

use Slim\App;
use Middlewares\TrailingSlash;
use App\Middlewares\ReturningJsonMiddleware;

return function (App $app) {
    $app->add(new TrailingSlash(trailingSlash: false));
    $app->add(new ReturningJsonMiddleware());
};
