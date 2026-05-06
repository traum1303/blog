<?php
declare(strict_types=1);

use App\Core\Migrations\MigrationInterface;
use App\Core\Migrations\Blueprint;
use App\Core\Migrations\Schema;

return new class implements MigrationInterface {
    public function up(PDO $db): void
    {
        Schema::create($db, 'categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });
    }

    public function down(PDO $db): void
    {
        Schema::dropIfExists($db, 'categories');
    }
};

