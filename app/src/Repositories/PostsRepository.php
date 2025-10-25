<?php

declare(strict_types=1);

final class PostsRepository extends AbstractRepository
{
    public function create(string $slug, string $title, string $bodyHtml, ?string $publishedAt): int
    {
        $sql = 'INSERT INTO posts (slug, title, body_html, published_at, created_at, updated_at) VALUES (:slug, :title, :body_html, :published_at, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);
        $now = $this->now();
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        $stmt->bindValue(':published_at', $publishedAt, $publishedAt === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', $now, PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $slug, string $title, string $bodyHtml, ?string $publishedAt): void
    {
        $sql = 'UPDATE posts SET slug = :slug, title = :title, body_html = :body_html, published_at = :published_at, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':body_html', $bodyHtml, PDO::PARAM_STR);
        if ($publishedAt === null) {
            $stmt->bindValue(':published_at', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':published_at', $publishedAt, PDO::PARAM_STR);
        }
        $stmt->bindValue(':updated_at', $this->now(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug = :slug');
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}
