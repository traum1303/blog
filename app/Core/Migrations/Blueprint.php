<?php
declare(strict_types=1);

namespace App\Core\Migrations;

final class Blueprint
{
    /** @var ColumnDefinition[] */
    private array $columns = [];
    /** @var string[] */
    private array $primary = [];

    public function id(string $name = 'id'): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'INT AUTO_INCREMENT PRIMARY KEY');
        $this->columns[] = $column;
        return $column;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $column = new ColumnDefinition($name, "VARCHAR({$length})");
        $this->columns[] = $column;
        return $column;
    }

    public function text(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'TEXT');
        $this->columns[] = $column;
        return $column;
    }

    public function integer(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'INT');
        $this->columns[] = $column;
        return $column;
    }

    public function timestamp(string $name): ColumnDefinition
    {
        $column = new ColumnDefinition($name, 'TIMESTAMP');
        $this->columns[] = $column;
        return $column;
    }

    public function primary(array $columns): void
    {
        $this->primary = $columns;
    }

    public function toSql(): string
    {
        $parts = array_map(
            static fn (ColumnDefinition $column): string => $column->toSql(),
            $this->columns
        );

        if ($this->primary !== []) {
            $parts[] = 'PRIMARY KEY (' . implode(', ', $this->primary) . ')';
        }

        return implode(",\n    ", $parts);
    }
}

