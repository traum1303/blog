<?php declare(strict_types=1);

namespace App\Services;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostService {
    public function __construct(private readonly PostRepositoryInterface $postRepo) {}

    public function getAllPosts(\App\Core\Request $request): array
    {
        $page = (int)$request->query('page', 1);
        $sort = $request->query('sort', 'date');
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $posts = $this->postRepo->getAllWithPagination($sort, $limit, $offset);
        $total = $this->postRepo->count();

        return [
            'items' => $posts,
            'total' => $total,
            'limit' => $limit,
            'page' => $page,
        ];
    }
}
