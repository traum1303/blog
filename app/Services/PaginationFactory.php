<?php declare(strict_types=1);

namespace App\Services;

class PaginationFactory
{
    public function make(
        array $items,
        int $total,
        int $perPage,
        int $currentPage
    ): Paginator {
        return new Paginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            strtok($_SERVER['REQUEST_URI'], '?'),
            $_GET
        );
    }
}