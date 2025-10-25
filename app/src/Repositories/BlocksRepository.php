<?php

declare(strict_types=1);

final class BlocksRepository extends AbstractRepository
{
    public function create(string $identifier, string $bodyHtml): int
    {
        $sql = 'INSERT INTO content_blocks (identifier, body_html, created_at, updated_at) VALUES (:identifier, :body_html, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $now = $this->now();
        $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $identifier, string $bodyHtml): void
    {
        $sql = 'UPDATE content_blocks SET identifier = :identifier, body_html = :body_html, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $this->now(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM content_blocks WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findByIdentifier(string $identifier): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM content_blocks WHERE identifier = :identifier');
        $stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}
