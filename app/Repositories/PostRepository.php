<?php declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Category;
use PDO;
use App\Entities\Post;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected string $table = 'posts';

    public function getByCategory(
        int    $categoryId,
        string $sort,
        int    $limit,
        int    $offset
    ): array {

        $orderBy = match ($sort) {
            'views' => 'p.views DESC',
            default => 'p.created_at DESC',
        };

        $sql = "
            SELECT p.*
            FROM {$this->table} p
            INNER JOIN post_category pc 
                ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map([$this, 'map'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countByCategory(int $categoryId): int
    {
        $sql = "
            SELECT COUNT(DISTINCT p.id)
            FROM {$this->table} p
            INNER JOIN post_category pc 
                ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function findByCategory(int $categoryId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.* FROM {$this->table} p
            JOIN post_category pc ON p.id = pc.post_id
            WHERE pc.category_id = ?
        ");

        $stmt->execute([$categoryId]);

        return array_map([$this, 'map'], $stmt->fetchAll());
    }

    public function getLatest(int $limit = 3): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table}
            ORDER BY created_at DESC
            LIMIT ?
        ");

        $stmt->execute([$limit]);

        return array_map([$this, 'map'], $stmt->fetchAll());
    }

    public function incrementViews(Post $post): void
    {
        $this->pdo
            ->prepare("UPDATE {$this->table} SET views = views + 1 WHERE id = ? ")
            ->execute([$post->getId()]);
    }

    public function attachCategory(int $postId, int $categoryId): bool
    {
        return $this->pdo->prepare("
            INSERT INTO post_category (post_id, category_id)
            VALUES (?, ?)
        ")->execute([$postId, $categoryId]);
    }

    public function findWithCategories(int $id): ?Post
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            p.*, 
            c.id as c_id, 
            c.name as c_name, 
            c.description as c_description
        FROM {$this->table} p
        LEFT JOIN post_category pc ON p.id = pc.post_id
        LEFT JOIN categories c ON c.id = pc.category_id
        WHERE p.id = ?
    ");

        $stmt->execute([$id]);

        $rows = $stmt->fetchAll();

        if (!$rows) return null;

        $post = $this->map($rows[0]);

        foreach ($rows as $row) {
            if (isset($row['c_id']))
            {
                $category = new Category(
                    $row['c_id'],
                    $row['c_name'],
                    $row['c_description']
                );

                $post->addCategory($category);
            }
        }

        return $post;
    }

    public function getAllWithPagination(mixed $sort, int $limit, int $offset): array
    {
        $orderBy = match ($sort) {
            'views' => 'views',
            default => 'created_at',
        };

        $data = $this->orderBy($orderBy, 'DESC')->paginate($limit, $offset);

        return array_map([$this, 'map'], $data);
    }

    public function getRelatedPosts(int $postId, int $limit = 3): array
    {
        $sql = "
        SELECT DISTINCT p.*
        FROM $this->table p
        INNER JOIN post_category pc1 
            ON p.id = pc1.post_id
        WHERE pc1.category_id IN (
            SELECT category_id 
            FROM post_category 
            WHERE post_id = :post_id
        )
        AND p.id != :post_id
        ORDER BY p.created_at DESC
        LIMIT :limit
    ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return array_map([$this, 'map'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    protected function map(array $data): Post
    {
        return new Post(
            $data['id'],
            $data['title'],
            $data['description'],
            $data['content'],
            $data['image'],
            $data['views'],
            $data['created_at']
        );
    }
}