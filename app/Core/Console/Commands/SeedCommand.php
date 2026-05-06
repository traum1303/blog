<?php declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\CommandHandler;
use Database\Seeders\SeedRunner;

final class SeedCommand implements CommandHandler
{
    public function handle(array $argv): int
    {
        SeedRunner::seed();

        return 0;
    }
}

