<?php declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\CommandHandler;
use InvalidArgumentException;
use RuntimeException;

final class MakeMigrationCommand implements CommandHandler
{

    public function handle(array $argv): int
    {
        $name = $argv[2] ?? null;
        if (!$name) {
            throw new InvalidArgumentException('Missing migration name. Example: make:migration create_users_table');
        }

        $dir = base_path('database/migrations') ;
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Cannot create migrations directory: {$dir}");
        }

        $slug = strtolower(trim((string)$name));
        $slug = preg_replace('/[^a-z0-9_]+/', '_', $slug) ?: 'migration';
        $slug = trim($slug, '_');

        $ts = time();
        do {
            $date = date('Y_m_d', $ts);
            $time = date('His', $ts);
            $filename = "{$date}_{$time}_{$slug}.php";
            $path = $dir . '/' . $filename;
            $ts++;
        } while (file_exists($path));

        $template = <<<PHP
<?php
declare(strict_types=1);

use App\\Core\\Migrations\\Blueprint;
use App\\Core\\Migrations\\MigrationInterface;
use App\\Core\\Migrations\\Schema;

return new class implements MigrationInterface {
    public function up(PDO \$db): void
    {
        Schema::create(\$db, 'table_name', function (Blueprint \$table): void {
            \$table->id();
            // \$table->string('name')->nullable();
        });
    }

    public function down(PDO \$db): void
    {
        Schema::dropIfExists(\$db, 'table_name');
    }
};

PHP;

        file_put_contents($path, $template, LOCK_EX);
        echo "Created migration: {$filename}\n";

        return 0;
    }
}

