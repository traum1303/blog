<?php declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\CommandHandler;
use App\Core\Migrations\MigrationRepository;
use App\Core\Migrations\Migrator;
use PDO;

final class MigrateCommand implements CommandHandler
{
    public function handle(array $argv): int
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        $db = $app->make(PDO::class);
        $migrator = new Migrator($db, new MigrationRepository($db));
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

