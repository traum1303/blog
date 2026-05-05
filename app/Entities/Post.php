<?php declare(strict_types=1);

namespace App\Entities;

class Post extends BaseEntity
{
    private string $title;
    private string $description;
    private string $content;
    private ?string $image;
    private ?string $created_at;
    private int $views;

    /** @var Category[] */
    private array $categories = [];

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        string $content,
        ?string $image = null,
        int $views = 0,
        string $created_at = null,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->image = $image;
        $this->views = $views;
        $this->created_at = $created_at;
    }

    // --- getters ---
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getContent(): string { return $this->content; }
    public function getImage(): ?string { return $this->image; }
    public function getViews(): int { return $this->views; }
    public function getCreatedAt(): string { return $this->created_at; }
    public function getCategories(): array { return $this->categories; }

    // --- relations ---
    public function addCategory(Category $category): void
    {
        $this->categories[] = $category;
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function incrementViews(): void
    {
        $this->views++;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'views' => $this->views,
        ];
    }
}