<?php declare(strict_types=1);

namespace App\Providers;

use App\Core\Application;
use App\Core\Console\Commands\MigrateCommand;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\PostRepository;
use App\Services\PaginationFactory;
use PDO;

class AppServiceProvider
{
    public static function register(Application $container)
    {
        $container->singleton(PaginationFactory::class, new PaginationFactory());

        $container->bind(
            PostRepositoryInterface::class,
            PostRepository::class
        );

        $container->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
    }
}