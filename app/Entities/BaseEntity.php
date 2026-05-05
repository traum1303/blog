<?php

namespace App\Entities;

abstract class BaseEntity
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }

    abstract public function toArray(): array;
}