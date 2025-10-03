<?php require_once __DIR__ . "/../../../includes/auth.php"; auth(); ?>
<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$items = $db->query("SELECT * FROM menu_items ORDER BY position ASC")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Menu</title></head><body>
<h1>📋 Pozycje menu</h1>
<p><a href="add.php">➕ Dodaj nową</a></p>
<table border="1" cellpadding="5">
<tr><th>#</th><th>Label</th><th>Typ</th><th>Target</th><th>Widoczność</th><th>Pozycja</th><th>Akcje</th></tr>
<?php foreach ($items as $i): ?>
<tr>
<td><?=$i['id']?></td>
<td><?=htmlspecialchars($i['label'])?></td>
<td><?=$i['type']?></td>
<td><?=htmlspecialchars($i['target'])?></td>
<td><?=$i['visible'] ? '✅' : '❌'?></td>
<td>
  <a href="move.php?id=<?=$i['id']?>&dir=up">⬆️</a>
  <a href="move.php?id=<?=$i['id']?>&dir=down">⬇️</a>
</td>
<td>
  <a href="edit.php?id=<?=$i['id']?>">✏️</a>
  <a href="delete.php?id=<?=$i['id']?>" onclick="return confirm('Usunąć?')">🗑️</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</body></html>
