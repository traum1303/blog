<?php
declare(strict_types=1);

namespace App\Core\Migrations;

use PDO;
use RuntimeException;

final class Migrator
{
    public function __construct(
        private readonly PDO $db,
        private readonly MigrationRepository $repo
    ) {
    }

    public function migrate(string $migrationsPath): array
    {
        $this->repo->ensureMigrationsTable();

        $files = $this->migrationFiles($migrationsPath);
        if ($files === []) {
            return [];
        }

        $batch = $this->repo->nextBatch();
        $applied = [];

        foreach ($files as $file) {
            $name = basename($file);

            if ($this->repo->has($name)) {
                continue;
            }

            $migration = require $file;

            if (!$migration instanceof MigrationInterface) {
                throw new RuntimeException("Migration file must return MigrationInterface: {$file}");
            }

            $migration->up($this->db);
            $this->repo->log($name, $batch);
            $applied[] = $name;
        }

        return $applied;
    }

    private function migrationFiles(string $migrationsPath): array
    {
        if (!is_dir($migrationsPath)) {
            throw new RuntimeException("Migrations directory not found: {$migrationsPath}");
        }

        $files = glob(rtrim($migrationsPath, '/') . '/*.php') ?: [];
        $files = array_values(array_filter($files, static function (string $file): bool {
            $base = basename($file);
            return (bool)preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_.+\.php$/', $base);
        }));

        sort($files, SORT_STRING);

        return $files;
    }
}

