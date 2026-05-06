<?php
declare(strict_types=1);

namespace App\Core\Migrations;

final class ColumnDefinition
{
    private bool $nullable = false;
    private mixed $default = null;
    private bool $hasDefault = false;
    private bool $useCurrent = false;

    public function __construct(
        private readonly string $name,
        private readonly string $type
    ) {
    }

    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;
        return $this;
    }

    public function default(mixed $value): self
    {
        $this->default = $value;
        $this->hasDefault = true;
        return $this;
    }

    public function useCurrent(): self
    {
        $this->useCurrent = true;
        return $this;
    }

    public function toSql(): string
    {
        $sql = "{$this->name} {$this->type}";
        $sql .= $this->nullable ? ' NULL' : ' NOT NULL';

        if ($this->useCurrent) {
            $sql .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif ($this->hasDefault) {
            if (is_string($this->default)) {
                $escaped = str_replace("'", "''", $this->default);
                $sql .= " DEFAULT '{$escaped}'";
            } elseif ($this->default === null) {
                $sql .= ' DEFAULT NULL';
            } elseif (is_bool($this->default)) {
                $sql .= ' DEFAULT ' . ($this->default ? '1' : '0');
            } else {
                $sql .= ' DEFAULT ' . (string)$this->default;
            }
        }

        return $sql;
    }
}

