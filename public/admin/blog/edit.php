<?php require_once __DIR__ . "/../../includes/layout.php"; ?>
<?php require_once __DIR__ . "/../../includes/auth.php"; auth(); ?>
<?php
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { echo "❌ Nie znaleziono wpisu."; exit; }
?>
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
