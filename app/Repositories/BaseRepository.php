<?php declare(strict_types=1);

namespace App\Repositories;

use App\Entities\BaseEntity;
use App\Repositories\Contracts\RepositoryInterface;
use PDO;
use Exception;

abstract class BaseRepository implements RepositoryInterface
{
    protected PDO $pdo;
    protected string $table;
    protected array $wheres = [];
    protected array $bindings = [];
    protected string $orderBy = '';
    protected ?int $limitValue = null;
    protected ?int $offsetValue = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    abstract protected function map(array $data): BaseEntity;

    // ===== BASIC =====

    public function all(): array
    {
        return $this->query()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): BaseEntity|null
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findOrFail(int $id): BaseEntity|null
    {
        $result = $this->find($id);

        if (!$result) {
            throw new Exception("Record not found");
        }

        return $result;
    }

    public function findBy(array $criteria): array
    {
        $this->applyCriteria($criteria);
        $res = [];

        foreach ($this->get() as $entity) {
            $res[] = $this->map($entity);
        }

        return $res;
    }

    public function findOneBy(array $criteria): BaseEntity|null
    {
        $this->applyCriteria($criteria);
        $this->limit(1);

        $result = $this->get();

        return  $this->map($result[0]) ?? null;
    }

    public function exists(array $criteria): bool
    {
        return $this->countWhere($criteria) > 0;
    }

    // ===== CREATE =====

    public function save(BaseEntity $entity): int
    {
        if ($entity->isNew()) {
            $id = $this->create($entity->toArray());
            $entity->setId($id);

            return $id;
        }

        $id = $entity->getId();

        $this->update($id, $entity->toArray());

        return $id;
    }

    public function insert(array $rows): bool
    {
        if (empty($rows)) return false;

        $fields = array_keys($rows[0]);
        $columns = implode(',', $fields);

        $placeholders = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
        $sql = "INSERT INTO {$this->table} ($columns) VALUES " .
            implode(',', array_fill(0, count($rows), $placeholders));

        $stmt = $this->pdo->prepare($sql);

        $flat = [];
        foreach ($rows as $row) {
            $flat = array_merge($flat, array_values($row));
        }

        return $stmt->execute($flat);
    }

    public function updateWhere(array $criteria, array $data): int
    {
        $set = implode(', ', array_map(fn($f) => "$f = :set_$f", array_keys($data)));

        $this->applyCriteria($criteria);

        $sql = "UPDATE {$this->table} SET $set " . $this->buildWhere();

        $stmt = $this->pdo->prepare($sql);

        $params = [];
        foreach ($data as $k => $v) {
            $params["set_$k"] = $v;
        }

        return $stmt->execute(array_merge($params, $this->bindings))
            ? $stmt->rowCount()
            : 0;
    }

    // ===== DELETE =====

    public function delete(int|BaseEntity $id): bool
    {
        return $this->deleteWhere(['id' => $id]) > 0;
    }

    public function deleteWhere(array $criteria): int
    {
        $this->applyCriteria($criteria);

        $sql = "DELETE FROM {$this->table} " . $this->buildWhere();
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    // ===== COUNT =====

    public function count(): int
    {
        return (int)$this->pdo
            ->query("SELECT COUNT(*) FROM {$this->table}")
            ->fetchColumn();
    }

    public function countWhere(array $criteria): int
    {
        $this->applyCriteria($criteria);

        $sql = "SELECT COUNT(*) FROM {$this->table} " . $this->buildWhere();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return (int)$stmt->fetchColumn();
    }

    // ===== QUERY BUILDER =====

    public function where(string $column, string $operator, mixed $value): static
    {
        $param = $column . count($this->bindings);
        $this->wheres[] = "$column $operator :$param";
        $this->bindings[$param] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limitValue = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offsetValue = $offset;
        return $this;
    }

    public function paginate(int $limit, int $offset = 0): array
    {
        return $this->limit($limit)->offset($offset)->get();
    }

    public function get(): array
    {
        $stmt = $this->query();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->reset();

        return $data;
    }

    public function reset(): static
    {
        $this->wheres = [];
        $this->bindings = [];
        $this->orderBy = '';
        $this->limitValue = null;
        $this->offsetValue = null;

        return $this;
    }

    public function getLastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    // ===== INTERNAL =====

    protected function query()
    {
        $sql = "SELECT * FROM {$this->table} "
            . $this->buildWhere()
            . " {$this->orderBy}";

        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }

        if ($this->offsetValue !== null) {
            $sql .= " OFFSET {$this->offsetValue}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt;
    }

    protected function applyCriteria(array $criteria): void
    {
        foreach ($criteria as $column => $value) {
            $this->where($column, '=', $value);
        }
    }

    protected function buildWhere(): string
    {
        if (empty($this->wheres)) return '';

        return 'WHERE ' . implode(' AND ', $this->wheres);
    }

    protected function create(array $data): int
    {
        $fields = array_keys($data);
        $columns = implode(',', $fields);
        $placeholders = ':' . implode(', :', $fields);

        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)"
        );

        $stmt->execute($data);

        return $this->getLastInsertId();
    }

    // ===== UPDATE =====

    protected function update(int $id, array $data): bool
    {
        return $this->updateWhere(['id' => $id], $data) > 0;
    }
}