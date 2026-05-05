<?php

namespace App\Repositories\Contracts;

use App\Entities\BaseEntity;
use App\Entities\Entity;

interface RepositoryInterface
{
    public function all(): array;

    public function find(int $id): null|array|BaseEntity;

    public function findOrFail(int $id): ?BaseEntity;

    public function findBy(array $criteria): array;

    public function findOneBy(array $criteria): ?BaseEntity;

    public function insert(array $rows): bool;

    public function updateWhere(array $criteria, array $data): int;

    public function delete(int $id): bool;

    public function deleteWhere(array $criteria): int;

    public function exists(array $criteria): bool;

    public function count(): int;

    public function countWhere(array $criteria): int;

    public function paginate(int $limit, int $offset = 0): array;

    public function orderBy(string $column, string $direction = 'ASC'): static;

    public function limit(int $limit): static;

    public function offset(int $offset): static;

    public function where(string $column, string $operator, mixed $value): static;

    public function reset(): static;

    public function getLastInsertId(): int;
}