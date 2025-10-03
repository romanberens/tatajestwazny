<?php require 'csrf.php'; auth(); ?>
<!doctype html><html><head><meta charset="utf-8"><title>Nowa strona</title></head><body>
<h1>➕ Dodaj nową stronę</h1>
<form method="post" action="save.php">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <p>Slug (np. jak-pomagam): <input name="slug" required></p>
  <p>Tytuł: <input name="title" required></p>
  <p>HTML:</p>
  <textarea name="body_html" rows="10" cols="80"></textarea>
  <p><button type="submit">💾 Zapisz</button></p>
</form>
<p><a href="index.php">← Wróć</a></p>
</body></html>
