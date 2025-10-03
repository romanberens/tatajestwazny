<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$id]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$page) die("Nie znaleziono.");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edycja</title></head><body>
<h1>✏️ Edycja: <?=htmlspecialchars($page['title'])?></h1>
<form method="post" action="save.php">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="id" value="<?=$page['id']?>">
  <p>Slug: <input name="slug" value="<?=htmlspecialchars($page['slug'])?>" required></p>
  <p>Tytuł: <input name="title" value="<?=htmlspecialchars($page['title'])?>" required></p>
  <p>HTML:</p>
  <textarea name="body_html" rows="10" cols="80"><?=htmlspecialchars($page['body_html'])?></textarea>
  <p><button type="submit">💾 Zapisz zmiany</button></p>
</form>
<p><a href="index.php">← Wróć</a></p>
</body></html>
