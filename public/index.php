<?php

use App\Core\Request;

$app = require __DIR__ . '/../bootstrap/app.php';
$app->handleRequest(Request::capture(), $router);
