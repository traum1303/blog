<?php
namespace App\Repositories\Contracts;
use App\Entities\Post;

interface PostRepositoryInterface {

    public function findByCategory(int $categoryId): array;
    public function getLatest(int $limit = 3): array;
    public function incrementViews(Post $post): void;
    public function attachCategory(int $postId, int $categoryId): bool;
}
