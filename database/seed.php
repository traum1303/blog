<?php

$app = require __DIR__ . '/../bootstrap/app.php';

$seeder = $app->make(Database\Seeders\DatabaseSeeder::class);

$seeder->run();

echo "Seeding completed \n";