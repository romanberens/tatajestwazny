<?php
require 'csrf.php';
auth();
$db = new PDO('sqlite:../../db/tjw.sqlite');
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM content_blocks WHERE id = ?");
$stmt->execute([$id]);
$block = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$block) die("Nie znaleziono bloku.");
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edycja</title></head><body>
<h1>✏️ Edycja bloku ID <?=$id?></h1>
<form method="post" action="save.php">
  <input type="hidden" name="id" value="<?=$id?>">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <p>Tytuł: <input name="title" value="<?=htmlspecialchars($block['title'])?>" style="width:300px"></p>
  <p>HTML:</p>
  <textarea name="body_html" rows="10" cols="80"><?=htmlspecialchars($block['body_html'])?></textarea>
  <p><button type="submit">💾 Zapisz</button></p>
</form>
<p><a href="index.php">← Powrót</a></p>
</body></html>
