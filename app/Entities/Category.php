<?php declare(strict_types=1);

namespace App\Entities;

class Category extends BaseEntity
{
    private string $name;
    private string $description;

    /** @var Post[] */
    private array $posts = [];

    public function __construct(
        ?int $id,
        string $name,
        string $description
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }

    public function addPost(Post $post): void
    {
        $this->posts[] = $post;
    }

    public function getPosts(): array
    {
        return $this->posts;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}