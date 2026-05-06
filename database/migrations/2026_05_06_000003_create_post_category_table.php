<?php
declare(strict_types=1);

use App\Core\Migrations\MigrationInterface;
use App\Core\Migrations\Blueprint;
use App\Core\Migrations\Schema;

return new class implements MigrationInterface {
    public function up(PDO $db): void
    {
        Schema::create($db, 'post_category', function (Blueprint $table): void {
            $table->integer('post_id');
            $table->integer('category_id');
            $table->primary(['post_id', 'category_id']);
        });
    }

    public function down(PDO $db): void
    {
        Schema::dropIfExists($db, 'post_category');
    }
};

