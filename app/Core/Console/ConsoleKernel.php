<?php declare(strict_types=1);

namespace App\Core\Console;

use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\SeedCommand;
use InvalidArgumentException;

final class ConsoleKernel
{
    public function __construct(private readonly string $projectRoot)
    {
    }

    public function run(array $argv): int
    {
        $command = $argv[1] ?? null;

        if ($command === null || in_array($command, ['--help', '-h'], true)) {
            $this->printHelp($argv);
            return 0;
        }

        $handlers = [
            'migrate' => new MigrateCommand(),
            'seed' => new SeedCommand(),
            'make:migration' => new MakeMigrationCommand($this->projectRoot),
        ];

        $handler = $handlers[$command] ?? null;
        if (!$handler instanceof CommandHandler) {
            throw new InvalidArgumentException("Unknown command: {$command}");
        }

        return $handler->handle($argv);
    }

    private function printHelp(array $argv): void
    {
        $script = $argv[0] ?? 'artisan';
        echo "Usage:\n";
        echo "  {$script} migrate\n";
        echo "  {$script} seed\n";
        echo "  {$script} make:migration <name>\n";
        echo "\n";
        echo "Options:\n";
        echo "  --help, -h    Show this help\n";
    }
}

