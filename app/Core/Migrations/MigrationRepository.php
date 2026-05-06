<?php
declare(strict_types=1);

namespace App\Core\Migrations;

use PDO;

final class MigrationRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function ensureMigrationsTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS migrations
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    migration  VARCHAR(255) NOT NULL UNIQUE,
    batch      INT NOT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

        $this->db->exec($sql);
    }

    public function has(string $migration): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM migrations WHERE migration = ? LIMIT 1');
        $stmt->execute([$migration]);
        return (bool)$stmt->fetchColumn();
    }

    public function nextBatch(): int
    {
        $batch = (int)$this->db->query('SELECT COALESCE(MAX(batch), 0) FROM migrations')->fetchColumn();
        return $batch + 1;
    }

    public function log(string $migration, int $batch): void
    {
        $stmt = $this->db->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
        $stmt->execute([$migration, $batch]);
    }
}

