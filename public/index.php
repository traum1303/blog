<?php

use App\Core\Request;

$app = require __DIR__ . '/../bootstrap/app.php';
$app->singleton(\App\Core\Router::class, new \App\Core\Router());
$router = $app->make(\App\Core\Router::class);

$app->loadRoutes('routes/routes.php')->create();
$app->handleRequest(Request::capture(), $router);
