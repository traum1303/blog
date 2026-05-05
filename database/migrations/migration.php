<?php

$app = require __DIR__ . '/../../bootstrap/app.php';

$db = $app->make(PDO::class);

$sql = 'create table categories
(
    id          int auto_increment primary key,
    name        varchar(255) null,
    description text         null
);

create table post_category
(
    post_id     int not null,
    category_id int not null,
    primary key (post_id, category_id)
);

create table posts
(
    id          int auto_increment           primary key,
    title       varchar(255)                        null,
    description text                                null,
    content     text                                null,
    image       varchar(255)                        null,
    views       int       default 0                 null,
    created_at  timestamp default CURRENT_TIMESTAMP null
);';


$db->exec($sql);