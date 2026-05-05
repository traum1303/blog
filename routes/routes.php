<?php

use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Controllers\CategoryController;

global $router;

$router->get('/', [HomeController::class, 'index']);
$router->get('/posts', [PostController::class, 'index']);
$router->get('/post/{id}', [PostController::class, 'show']);
$router->get('/category/{id}', [CategoryController::class, 'show']);
$router->post('post/create', [PostController::class, 'create']);
