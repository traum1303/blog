<?php declare(strict_types=1);

namespace App\Services;

class Paginator
{
    public function __construct(
        private array $items,
        private int $total,
        private int $perPage,
        private int $currentPage,
        private string $path,
        private array $query = []
    ) {}

    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function lastPage(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function hasPages(): bool
    {
        return $this->lastPage() > 1;
    }

    public function url(int $page): string
    {
        return $this->path . '?' . http_build_query(array_merge(
                $this->query,
                ['page' => $page]
            ));
    }

    public function links(): string
    {
        if (!$this->hasPages()) return '';

        $html = '<div class="flex justify-center mt-6 space-x-1">';

        // Prev
        if ($this->currentPage > 1) {
            $html .= '<a class="px-3 py-1 border rounded" href="' . $this->url($this->currentPage - 1) . '">Prev</a>';
        }

        // Pages
        for ($i = max(1, $this->currentPage - 2); $i <= min($this->lastPage(), $this->currentPage + 2); $i++) {
            $active = $i == $this->currentPage
                ? 'bg-blue-500 text-white'
                : '';

            $html .= '<a class="px-3 py-1 border rounded ' . $active . '" href="' . $this->url($i) . '">' . $i . '</a>';
        }

        // Next
        if ($this->currentPage < $this->lastPage()) {
            $html .= '<a class="px-3 py-1 border rounded" href="' . $this->url($this->currentPage + 1) . '">Next</a>';
        }

        $html .= '</div>';

        return $html;
    }
}