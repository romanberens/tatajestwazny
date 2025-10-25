<?php

declare(strict_types=1);

final class PagesRepository extends AbstractRepository
{
    public function create(string $slug, string $title, string $bodyHtml): int
    {
        $sql = 'INSERT INTO pages (slug, title, body_html, created_at, updated_at) VALUES (:slug, :title, :body_html, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $now = $this->now();
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $slug, string $title, string $bodyHtml): void
    {
        $sql = 'UPDATE pages SET slug = :slug, title = :title, body_html = :body_html, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $this->now(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM pages WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE slug = :slug');
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}
