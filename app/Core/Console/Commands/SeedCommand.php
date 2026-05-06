<?php declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Application;
use App\Core\Console\CommandHandler;
use Database\Seeders\DatabaseSeeder;

final class SeedCommand implements CommandHandler
{

    public function __construct(private readonly DatabaseSeeder $seeder){}


    public function handle(array $argv): int
    {

        $this->seeder->run();

        echo "Seeding completed \n";

        return 0;
    }
}

