<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
csrf_check($_POST['csrf'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body_html'] ?? '');
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

if (!$slug || !$title || !$body) die("Brak danych.");

if ($id) {
  $stmt = $db->prepare("UPDATE pages SET slug=?, title=?, body_html=?, updated_at=datetime('now') WHERE id=?");
  $stmt->execute([$slug, $title, $body, $id]);
} else {
  $stmt = $db->prepare("INSERT INTO pages(slug, title, body_html) VALUES (?, ?, ?)");
  $stmt->execute([$slug, $title, $body]);
}
header("Location: index.php");
exit;
