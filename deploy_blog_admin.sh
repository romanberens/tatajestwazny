#!/bin/bash
set -e

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
ADMIN_BLOG_DIR="$BASE_DIR/public/admin/blog"
DB="$BASE_DIR/db/tjw.sqlite"

echo "�� Tworzenie katalogu admin/blog..."
mkdir -p "$ADMIN_BLOG_DIR"

echo "📄 Tworzenie pliku index.php..."
cat > "$ADMIN_BLOG_DIR/index.php" <<'PHP'
<?php
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$posts = $db->query("SELECT id, title, slug, published_at FROM posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Blog – Admin</title></head><body>
<h1>📚 Wpisy blogowe</h1>
<p><a href="add.php">➕ Dodaj nowy wpis</a></p>
<ul>
<?php foreach ($posts as $p): ?>
  <li>
    <strong><?= htmlspecialchars($p['title']) ?></strong>
    <?php if ($p['published_at']): ?>
      <small>(<?= $p['published_at'] ?>)</small>
    <?php else: ?>
      <small>(nieopublikowany)</small>
    <?php endif; ?>
    — <a href="edit.php?slug=<?= urlencode($p['slug']) ?>">✏️ Edytuj</a>
  </li>
<?php endforeach; ?>
</ul>
</body></html>
PHP

echo "📝 Tworzenie pliku add.php..."
cat > "$ADMIN_BLOG_DIR/add.php" <<'PHP'
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Dodaj wpis</title></head><body>
<h1>➕ Dodaj nowy wpis</h1>
<form action="save.php" method="post">
  <input type="hidden" name="mode" value="add">
  <p>Tytuł: <input type="text" name="title" required></p>
  <p>Slug (URL): <input type="text" name="slug" required></p>
  <p>Wstęp: <textarea name="excerpt"></textarea></p>
  <p>Treść (HTML): <textarea name="body_html" rows="10" cols="80"></textarea></p>
  <p>Opublikuj? <input type="checkbox" name="publish" value="1"></p>
  <button type="submit">�� Zapisz</button>
</form>
<p><a href="index.php">⬅️ Powrót</a></p>
</body></html>
PHP

echo "✏️ Tworzenie pliku edit.php..."
cat > "$ADMIN_BLOG_DIR/edit.php" <<'PHP'
<?php
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { echo "❌ Nie znaleziono wpisu."; exit; }
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Edytuj wpis</title></head><body>
<h1>✏️ Edytuj wpis: <?= htmlspecialchars($post['title']) ?></h1>
<form action="save.php" method="post">
  <input type="hidden" name="mode" value="edit">
  <input type="hidden" name="id" value="<?= $post['id'] ?>">
  <p>Tytuł: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>"></p>
  <p>Slug (URL): <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>"></p>
  <p>Wstęp: <textarea name="excerpt"><?= htmlspecialchars($post['excerpt']) ?></textarea></p>
  <p>Treść (HTML): <textarea name="body_html" rows="10" cols="80"><?= htmlspecialchars($post['body_html']) ?></textarea></p>
  <p>Opublikuj? <input type="checkbox" name="publish" value="1" <?= $post['published_at'] ? 'checked' : '' ?>></p>
  <button type="submit">💾 Zapisz zmiany</button>
</form>
<p><a href="index.php">⬅️ Powrót</a></p>
</body></html>
PHP

echo "💾 Tworzenie pliku save.php..."
cat > "$ADMIN_BLOG_DIR/save.php" <<'PHP'
<?php
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$mode = $_POST['mode'] ?? 'add';
$title = $_POST['title'] ?? '';
$slug = $_POST['slug'] ?? '';
$excerpt = $_POST['excerpt'] ?? '';
$body = $_POST['body_html'] ?? '';
$publish = isset($_POST['publish']) ? date('Y-m-d H:i:s') : null;

if ($mode === 'add') {
  $stmt = $db->prepare("INSERT INTO posts (title, slug, excerpt, body_html, published_at, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
  $stmt->execute([$title, $slug, $excerpt, $body, $publish]);
} elseif ($mode === 'edit') {
  $id = $_POST['id'];
  $stmt = $db->prepare("UPDATE posts SET title=?, slug=?, excerpt=?, body_html=?, published_at=?, updated_at=datetime('now') WHERE id=?");
  $stmt->execute([$title, $slug, $excerpt, $body, $publish, $id]);
}

header("Location: index.php");
exit;
PHP

echo "✅ Blog admin module deployed!"
