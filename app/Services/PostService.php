<?php declare(strict_types=1);

namespace App\Services;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostService {
    public function __construct(private readonly PostRepositoryInterface $postRepo) {}

    public function getAllPosts(\App\Core\Request $request): array
    {
        //TODO implement validator
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


    public function getRelatedPosts(int $postId, int $limit = 3): array
    {
        return $this->postRepo->getRelatedPosts($postId, $limit);
    }

    public function getPostWithIncrementingViews(int $id): ?\App\Entities\Post
    {
        $post = $this->postRepo->findWithCategories($id);
        $post->incrementViews();

        /* TODO
            implement job dispatcher in order to increment views
            asynchronic and implement cache for counting views using redis
         */
        $this->postRepo->incrementViews($post);

        return $post;
    }
}
