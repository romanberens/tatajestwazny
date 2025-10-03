<?php require_once __DIR__ . "/../../includes/layout.php"; ?>
<?php require_once __DIR__ . "/../../includes/auth.php"; auth(); ?>
<?php
$db = new PDO('sqlite:../../db/tjw.sqlite');

$slug = $_GET['slug'] ?? '';
if ($slug) {
  $stmt = $db->prepare("DELETE FROM posts WHERE slug = ?");
  $stmt->execute([$slug]);
}

header('Location: index.php');
exit;
?>
