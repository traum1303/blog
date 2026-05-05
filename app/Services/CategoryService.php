<?php declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private PostRepositoryInterface $postRepository
    ) {}

    public function getPostsByCategory(\App\Core\Request $request, int $id): array
    {
        $page = (int)$request->query('page', 1);
        $sort = $request->query('sort', 'date');
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $posts = $this->postRepository->getByCategory($id, $sort, $limit, $offset);
        $total = $this->postRepository->countByCategory($id);

        return [
            'items' => $posts,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
        ];
    }

    public function getWithPostsPaginated(\App\Core\Request $request): array
    {
        $page = (int)$request->query('page', 1);
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $categories = $this->categoryRepository->getWithPostsPaginated($limit, $offset);
        $total = $this->categoryRepository->countWhereHas();

        return [
            'items' => $categories,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
        ];
    }
}