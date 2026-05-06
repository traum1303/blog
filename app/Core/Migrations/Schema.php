<?php
declare(strict_types=1);

namespace App\Core\Migrations;

use Closure;
use PDO;

final class Schema
{
    public static function create(PDO $db, string $table, Closure $callback): void
    {
        $blueprint = new Blueprint();
        $callback($blueprint);

        $sql = "CREATE TABLE IF NOT EXISTS {$table}\n(\n    " .
            $blueprint->toSql() .
            "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->exec($sql);
    }

    public static function dropIfExists(PDO $db, string $table): void
    {
        $db->exec("DROP TABLE IF EXISTS {$table};");
    }
}

