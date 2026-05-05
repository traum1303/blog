<?php

use App\Core\Application;
use App\Providers\AppServiceProvider;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__));
$app->bootstrap();
$app->register(AppServiceProvider::class);
$app->singleton(\App\Core\Router::class, new \App\Core\Router());
$router = $app->make(\App\Core\Router::class);

$app->loadRoutes('routes/routes.php')->create();

return $app;