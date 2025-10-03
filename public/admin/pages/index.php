<?php require_once __DIR__ . "/../../../includes/auth.php"; auth(); ?>
<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$pages = $db->query("SELECT id, slug, title FROM pages ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Podstrony</title></head><body>
<h1>📚 Lista podstron</h1>
<p><a href="add.php">➕ Dodaj nową</a></p>
<ul>
<?php foreach ($pages as $p): ?>
  <li><strong><?=htmlspecialchars($p['title'])?></strong>
      — <code><?=htmlspecialchars($p['slug'])?></code>
      — <a href="edit.php?id=<?=$p['id']?>">Edytuj</a>
      — <a href="delete.php?id=<?=$p['id']?>" onclick="return confirm('Na pewno usunąć?')">🗑️</a>
  </li>
<?php endforeach; ?>
</ul>
</body></html>
