<?php

declare(strict_types=1);

final class MenuRepository extends AbstractRepository
{
    public function create(string $label, string $url, int $position): int
    {
        $sql = 'INSERT INTO menu_items (label, url, position, created_at, updated_at) VALUES (:label, :url, :position, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $now = $this->now();
        $stmt->bindValue(':label', $label, PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, PDO::PARAM_STR);
        $stmt->bindValue(':position', $position, PDO::PARAM_INT);
        $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $label, string $url, int $position): void
    {
        $sql = 'UPDATE menu_items SET label = :label, url = :url, position = :position, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':label', $label, PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, PDO::PARAM_STR);
        $stmt->bindValue(':position', $position, PDO::PARAM_INT);
        $stmt->bindValue(':updated_at', $this->now(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM menu_items WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM menu_items ORDER BY position ASC');
        return $stmt->fetchAll() ?: [];
    }
}
