<?php declare(strict_types=1);

namespace Database\Seeders;

use PDO;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;
use Database\Factories\PostFactory;
use Database\Factories\CategoryFactory;

class DatabaseSeeder
{
    public function __construct(
        private readonly PDO                $db,
        private readonly PostRepository     $postRepo,
        private readonly CategoryRepository $categoryRepo
    ){}

    public function run(): void
    {
        $this->truncate();

        $categoryIds = [];

        for ($i = 0; $i < 50; $i++) {
            $category = CategoryFactory::make();
            $categoryIds[] = $this->categoryRepo->save($category);
        }

        for ($i = 0; $i < 500; $i++) {

            $post = PostFactory::make();

            $postId = $this->postRepo->save($post);

            $count = rand(1, 3);

            for ($j = 0; $j < $count; $j++) {
                $categoryId = $categoryIds[array_rand($categoryIds)];
                try {
                    $this->postRepo->attachCategory($postId, $categoryId);
                }catch (\PDOException $e){
                    echo 'this post already belongs to the category'.PHP_EOL;
                }
            }
        }
    }

    private function truncate(): void
    {
        $this->db->exec("SET FOREIGN_KEY_CHECKS=0");
        $this->db->exec("TRUNCATE post_category");
        $this->db->exec("TRUNCATE posts");
        $this->db->exec("TRUNCATE categories");
        $this->db->exec("SET FOREIGN_KEY_CHECKS=1");
    }
}