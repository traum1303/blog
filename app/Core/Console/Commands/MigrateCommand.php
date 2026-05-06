<?php declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Application;
use App\Core\Console\CommandHandler;
use App\Core\Migrations\MigrationRepository;
use App\Core\Migrations\Migrator;
use PDO;

final class MigrateCommand implements CommandHandler
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function handle(array $argv): int
    {
        $migrator = new Migrator($this->db, new MigrationRepository($this->db));
        $applied = $migrator->migrate(base_path('database/migrations'));

        if ($applied === []) {
            echo "Nothing to migrate\n";
            return 0;
        }

        foreach ($applied as $name) {
            echo "Migrated: {$name}\n";
        }

        return 0;
    }
}

