<?php
declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\DatabaseSeeder;

/**
 * Seed runner entry.
 * Loaded via composer autoload.files from `database/seed.php`.
 */
final class SeedRunner
{
    public static function seed(): void
    {
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $seeder = $app->make(DatabaseSeeder::class);
        $seeder->run();

        echo "Seeding completed \n";
    }
}