<?php require_once __DIR__ . "/../../includes/layout.php"; ?>
<?php require_once __DIR__ . "/../../includes/auth.php"; auth(); ?>
  <link rel="stylesheet" href="/style.css">
  <h1>➕ Dodaj nowy wpis</h1>
  <form action="save.php" method="post">
    <input type="hidden" name="mode" value="add">
    
    <p><label>Tytuł: <input type="text" name="title" required></label></p>
    <p><label>Slug (URL): <input type="text" name="slug" required></label></p>
    <p><label>Wstęp: <textarea name="excerpt" rows="3" cols="80"></textarea></label></p>
    <p><label>Treść (HTML): <textarea name="body_html" rows="10" cols="80"></textarea></label></p>
    <p><label>Opublikuj? <input type="checkbox" name="publish" value="1"></label></p>
    
    <button type="submit">💾 Zapisz</button>
  </form>
  <p><a href="index.php">⬅️ Powrót</a></p>
