<?php require_once __DIR__ . "/../../includes/layout.php"; ?>
<?php require_once __DIR__ . "/../../includes/auth.php"; auth(); ?>
<?php

$db = new PDO('sqlite:../../../db/tjw.sqlite');

$mode    = $_POST['mode'] ?? 'add';
$title   = $_POST['title'] ?? '';
$slug    = $_POST['slug'] ?? '';
$excerpt = $_POST['excerpt'] ?? '';
$body    = $_POST['body_html'] ?? '';
$publish = isset($_POST['publish']) ? date('Y-m-d H:i:s') : null;

if ($mode === 'add') {
  // 🛡️ Sprawdź, czy slug już istnieje
  $check = $db->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
  $check->execute([$slug]);
  if ($check->fetchColumn() > 0) {
    die("❌ Slug już istnieje!");
  }

  $stmt = $db->prepare("INSERT INTO posts (title, slug, excerpt, body_html, published_at, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
  $stmt->execute([$title, $slug, $excerpt, $body, $publish]);

} elseif ($mode === 'edit') {
  $id = $_POST['id'];
  $stmt = $db->prepare("UPDATE posts SET title=?, slug=?, excerpt=?, body_html=?, published_at=?, updated_at=datetime('now') WHERE id=?");
  $stmt->execute([$title, $slug, $excerpt, $body, $publish, $id]);
}

header("Location: index.php?msg=success");
exit;
