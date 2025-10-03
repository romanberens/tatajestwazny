<?php

final class PostsRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, title, slug, published_at, updated_at FROM posts ORDER BY COALESCE(published_at, updated_at) DESC');
        return $stmt->fetchAll();
    }

    public function published(): array
    {
        $stmt = $this->pdo->query('SELECT title, slug, excerpt, published_at FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug = ? AND published_at IS NOT NULL');
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function save(array $payload): int
    {
        $title = trim($payload['title'] ?? '');
        $slug = trim($payload['slug'] ?? '');
        $excerpt = trim($payload['excerpt'] ?? '');
        $body = $payload['body_html'] ?? '';
        $publishedAt = trim($payload['published_at'] ?? '');

        if ($title === '' || $slug === '' || $body === '') {
            throw new InvalidArgumentException('Tytuł, slug i treść są wymagane.');
        }

        $publishedAt = $publishedAt !== '' ? $publishedAt : null;

        if (!empty($payload['id'])) {
            $stmt = $this->pdo->prepare('UPDATE posts SET title = ?, slug = ?, excerpt = ?, body_html = ?, published_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$title, $slug, $excerpt, $body, $publishedAt, (int) $payload['id']]);

            return (int) $payload['id'];
        }

        $stmt = $this->pdo->prepare('INSERT INTO posts(title, slug, excerpt, body_html, published_at) VALUES(?,?,?,?,?)');
        $stmt->execute([$title, $slug, $excerpt, $body, $publishedAt]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
    }
}
