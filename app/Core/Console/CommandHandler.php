<?php declare(strict_types=1);

namespace App\Core\Console;

interface CommandHandler
{
    public function handle(array $argv): int;
}

