<?php

final class MenuRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM menu_items ORDER BY position ASC, id ASC');
        return $stmt->fetchAll();
    }

    public function visible(): array
    {
        $stmt = $this->pdo->query('SELECT label, target, type FROM menu_items WHERE visible = 1 ORDER BY position ASC, id ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        return $item ?: null;
    }

    public function save(array $payload): int
    {
        $label = trim($payload['label'] ?? '');
        $type = $payload['type'] ?? 'internal';
        $target = trim($payload['target'] ?? '');
        $position = (int) ($payload['position'] ?? 0);
        $visible = !empty($payload['visible']) ? 1 : 0;

        if ($label === '' || $target === '') {
            throw new InvalidArgumentException('Etykieta i adres są wymagane.');
        }

        if (!in_array($type, ['internal', 'external'], true)) {
            $type = 'internal';
        }

        if (!empty($payload['id'])) {
            $stmt = $this->pdo->prepare('UPDATE menu_items SET label = ?, type = ?, target = ?, position = ?, visible = ? WHERE id = ?');
            $stmt->execute([$label, $type, $target, $position, $visible, (int) $payload['id']]);

            return (int) $payload['id'];
        }

        $stmt = $this->pdo->prepare('INSERT INTO menu_items(label, type, target, position, visible) VALUES(?,?,?,?,?)');
        $stmt->execute([$label, $type, $target, $position, $visible]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM menu_items WHERE id = ?');
        $stmt->execute([$id]);
    }
}
