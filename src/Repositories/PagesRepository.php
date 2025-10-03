<?php

final class PagesRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, slug, title, updated_at FROM pages ORDER BY updated_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $page = $stmt->fetch();

        return $page ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE slug = ?');
        $stmt->execute([$slug]);
        $page = $stmt->fetch();

        return $page ?: null;
    }

    public function save(array $payload): int
    {
        $slug = trim($payload['slug'] ?? '');
        $title = trim($payload['title'] ?? '');
        $body = $payload['body_html'] ?? '';

        if ($slug === '' || $title === '' || $body === '') {
            throw new InvalidArgumentException('Slug, tytuł i treść nie mogą być puste.');
        }

        if (!empty($payload['id'])) {
            $stmt = $this->pdo->prepare('UPDATE pages SET slug = ?, title = ?, body_html = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$slug, $title, $body, (int) $payload['id']]);

            return (int) $payload['id'];
        }

        $stmt = $this->pdo->prepare('INSERT INTO pages(slug, title, body_html) VALUES(?, ?, ?)');
        $stmt->execute([$slug, $title, $body]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM pages WHERE id = ?');
        $stmt->execute([$id]);
    }
}
