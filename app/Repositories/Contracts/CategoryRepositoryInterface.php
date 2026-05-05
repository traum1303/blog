<?php

namespace App\Repositories\Contracts;


interface CategoryRepositoryInterface
{
    public function getWithPostsPaginated(int $limitCategories, int $offsetCategories, int $limitPosts = 3): array;

}