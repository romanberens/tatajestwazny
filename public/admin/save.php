<?php
require 'csrf.php';
auth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Błąd metody.");
csrf_check($_POST['csrf'] ?? '');
$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$body_html = trim($_POST['body_html'] ?? '');
if (!$id || !$title || !$body_html) die("Brak danych.");
$db = new PDO('sqlite:../../db/tjw.sqlite');
$stmt = $db->prepare("UPDATE content_blocks SET title = ?, body_html = ?, updated_at = datetime('now') WHERE id = ?");
$stmt->execute([$title, $body_html, $id]);
header("Location: index.php");
exit;
