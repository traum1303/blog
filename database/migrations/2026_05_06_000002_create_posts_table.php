<?php
declare(strict_types=1);

use App\Core\Migrations\MigrationInterface;
use App\Core\Migrations\Blueprint;
use App\Core\Migrations\Schema;

return new class implements MigrationInterface {
    public function up(PDO $db): void
    {
        Schema::create($db, 'posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->integer('views')->nullable()->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(PDO $db): void
    {
        Schema::dropIfExists($db, 'posts');
    }
};

