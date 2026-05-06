<?php

use App\Core\Application;
use App\Providers\AppServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__));
$app->bootstrap();
$app->register(AppServiceProvider::class);

return $app;