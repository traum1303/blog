<?php
declare(strict_types=1);

namespace App\Core\Migrations;

use PDO;

interface MigrationInterface
{
    public function up(PDO $db): void;
    public function down(PDO $db): void;
}

