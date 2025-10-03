<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$item = $db->query("SELECT * FROM menu_items WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
if (!$item) die("Nie znaleziono.");
$pages = $db->query("SELECT slug, title FROM pages ORDER BY title")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edycja menu</title></head><body>
<h1>✏️ Edytuj pozycję menu</h1>
<form method="post" action="save.php">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <input type="hidden" name="id" value="<?=$item['id']?>">
  <p>Label: <input name="label" value="<?=htmlspecialchars($item['label'])?>" required></p>
  <p>Typ:
    <select name="type" id="type" onchange="toggleTarget()">
      <option value="internal" <?=$item['type']=='internal'?'selected':''?>>Podstrona</option>
      <option value="external" <?=$item['type']=='external'?'selected':''?>>Link zewnętrzny</option>
    </select>
  </p>
  <div id="internal_target">
    <p>Wybierz stronę:
      <select name="target_internal">
        <?php foreach ($pages as $p): ?>
        <option value="<?=htmlspecialchars($p['slug'])?>" <?=$item['target']===$p['slug']?'selected':''?>><?=htmlspecialchars($p['title'])?></option>
        <?php endforeach; ?>
      </select>
    </p>
  </div>
  <div id="external_target" style="display:none;">
    <p>URL: <input name="target_external" value="<?=$item['type']==='external'?htmlspecialchars($item['target']):''?>"></p>
  </div>
  <p><label><input type="checkbox" name="visible" <?=$item['visible']?'checked':''?>> Widoczna w menu</label></p>
  <p><button type="submit">💾 Zapisz zmiany</button></p>
</form>
<script>toggleTarget();</script>
<script>
function toggleTarget() {
  const type = document.getElementById('type').value;
  document.getElementById('internal_target').style.display = type === 'internal' ? 'block' : 'none';
  document.getElementById('external_target').style.display = type === 'external' ? 'block' : 'none';
}
</script>
</body></html>
