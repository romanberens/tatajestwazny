<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$pages = $db->query("SELECT slug, title FROM pages ORDER BY title")->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Dodaj menu</title></head><body>
<h1>➕ Nowa pozycja menu</h1>
<form method="post" action="save.php">
  <input type="hidden" name="csrf" value="<?=csrf_token()?>">
  <p>Label: <input name="label" required></p>
  <p>Typ:
    <select name="type" id="type" onchange="toggleTarget()">
      <option value="internal">Podstrona</option>
      <option value="external">Link zewnętrzny</option>
    </select>
  </p>
  <div id="internal_target">
    <p>Wybierz stronę:
      <select name="target_internal">
        <?php foreach ($pages as $p): ?>
        <option value="<?=htmlspecialchars($p['slug'])?>"><?=htmlspecialchars($p['title'])?></option>
        <?php endforeach; ?>
      </select>
    </p>
  </div>
  <div id="external_target" style="display:none;">
    <p>URL: <input name="target_external" placeholder="https://example.com"></p>
  </div>
  <p><label><input type="checkbox" name="visible" checked> Widoczna w menu</label></p>
  <p><button type="submit">💾 Zapisz</button></p>
</form>
<script>
function toggleTarget() {
  const type = document.getElementById('type').value;
  document.getElementById('internal_target').style.display = type === 'internal' ? 'block' : 'none';
  document.getElementById('external_target').style.display = type === 'external' ? 'block' : 'none';
}
</script>
</body></html>
