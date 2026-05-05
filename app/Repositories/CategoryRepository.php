<?php declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Post;
use App\Entities\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected string $table = 'categories';

    public function getWithPostsPaginated(int $limitCategories, int $offsetCategories, int $limitPosts = 3): array
    {
        $stmt = $this->pdo->prepare("SELECT *
            FROM (
                 SELECT
                     c.id AS id,
                     c.name AS name,
                     c.description AS description,
                     p.id AS p_id,
                     p.title AS p_title,
                     p.description AS p_description,
                     p.content AS p_content,
                     p.image AS p_image,
                     p.views AS p_views,
                     p.created_at AS p_created_at,
                     pc.category_id AS join_category_id,
                     ROW_NUMBER() OVER (
                     PARTITION BY c.id
                     ORDER BY p.created_at DESC
                     ) AS rn
                FROM {$this->table} c
                JOIN post_category pc ON pc.category_id = c.id
                JOIN posts p ON p.id = pc.post_id
            ) t
            WHERE t.rn <= :limit_posts
            
            ORDER BY t.id, t.rn
            LIMIT :limit_categories OFFSET :offset_categories ");

        $stmt->bindValue(':limit_posts', $limitPosts, \PDO::PARAM_INT);
        $stmt->bindValue(':limit_categories', $limitCategories, \PDO::PARAM_INT);
        $stmt->bindValue(':offset_categories', $offsetCategories, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            if (!isset($row['id'])) {
                continue;
            }

            if (!isset($result[$row['id']])) {
                $result[$row['id']] = $this->map($row);
            }

            if (!isset($row['p_id'])) {
                continue;
            }

            $post = new Post(
                $row['p_id'],
                $row['p_title'],
                $row['p_description'],
                $row['p_content'],
                $row['p_image'],
                $row['p_views'],
                $row['p_created_at']
            );
            $result[$row['id']]->addPost($post);
        }

        return $result;
    }

    public function countWhereHas(int $limitPosts = 3): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*)
            FROM (
                SELECT ROW_NUMBER() OVER (PARTITION BY c.id) AS rn
                FROM {$this->table} c
                JOIN post_category pc ON pc.category_id = c.id
            ) t
            WHERE t.rn <= :limit_posts");

        $stmt->bindValue(':limit_posts', $limitPosts, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    protected function map(array $data): Category
    {
        return new Category(
            $data['id'],
            $data['name'],
            $data['description']
        );
    }
}