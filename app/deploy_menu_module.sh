#!/bin/bash
set -e

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
DB="$BASE_DIR/db/tjw.sqlite"
ADMIN_DIR="$BASE_DIR/public/admin/menu"
INCLUDES_DIR="$BASE_DIR/public/includes"

echo "📁 Tworzenie katalogu admin/menu..."
mkdir -p "$ADMIN_DIR"
mkdir -p "$INCLUDES_DIR"

echo "📦 Tworzenie tabeli menu_items..."
sqlite3 "$DB" '
CREATE TABLE IF NOT EXISTS menu_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  label TEXT NOT NULL,
  type TEXT NOT NULL CHECK(type IN ("internal", "external")),
  target TEXT NOT NULL,
  position INTEGER NOT NULL DEFAULT 0,
  visible INTEGER NOT NULL DEFAULT 1,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);'

echo "🔗 Dodawanie csrf.php (jeśli brak)..."
[ -f "$BASE_DIR/public/admin/csrf.php" ] && cp "$BASE_DIR/public/admin/csrf.php" "$ADMIN_DIR/csrf.php"

echo "📄 Generowanie plików panelu menu..."

# index.php
cat > "$ADMIN_DIR/index.php" <<'PHP'
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
PHP

# add.php
cat > "$ADMIN_DIR/add.php" <<'PHP'
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
PHP

# save.php
cat > "$ADMIN_DIR/save.php" <<'PHP'
<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
csrf_check($_POST['csrf'] ?? '');

$label = trim($_POST['label'] ?? '');
$type = $_POST['type'] ?? '';
$target = $type === 'internal' ? ($_POST['target_internal'] ?? '') : ($_POST['target_external'] ?? '');
$visible = isset($_POST['visible']) ? 1 : 0;

if (!$label || !$type || !$target) die("❌ Brak danych");

if ($type === 'external' && !preg_match('#^https?://#', $target))
  die("❌ Błędny adres URL");

$maxPos = $db->query("SELECT COALESCE(MAX(position),0) FROM menu_items")->fetchColumn();
$stmt = $db->prepare("INSERT INTO menu_items(label, type, target, visible, position) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$label, $type, $target, $visible, $maxPos + 1]);

header("Location: index.php");
exit;
PHP

# edit.php
cat > "$ADMIN_DIR/edit.php" <<'PHP'
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
PHP

# delete.php
cat > "$ADMIN_DIR/delete.php" <<'PHP'
<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$db->prepare("DELETE FROM menu_items WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit;
PHP

# move.php
cat > "$ADMIN_DIR/move.php" <<'PHP'
<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$dir = $_GET['dir'] ?? 'down';
$db = new PDO('sqlite:../../../db/tjw.sqlite');

$item = $db->query("SELECT id, position FROM menu_items WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
if (!$item) exit;

$cmp = $dir === 'up' ? '<' : '>';
$order = $dir === 'up' ? 'DESC' : 'ASC';

$stmt = $db->prepare("SELECT id, position FROM menu_items WHERE position $cmp ? ORDER BY position $order LIMIT 1");
$stmt->execute([$item['position']]);
$swap = $stmt->fetch(PDO::FETCH_ASSOC);

if ($swap) {
  $db->prepare("UPDATE menu_items SET position = ? WHERE id = ?")->execute([$item['position'], $swap['id']]);
  $db->prepare("UPDATE menu_items SET position = ? WHERE id = ?")->execute([$swap['position'], $item['id']]);
}
header("Location: index.php");
exit;
PHP

# layout.php aktualizacja
cat > "$INCLUDES_DIR/layout.php" <<'PHP'
<?php
function layout($title, $body) {
  $db = new PDO('sqlite:../db/tjw.sqlite');
  $menu = $db->query("SELECT label, type, target FROM menu_items WHERE visible = 1 ORDER BY position")->fetchAll();
  echo "<!doctype html><html><head><meta charset='utf-8'><title>$title</title></head><body><nav>";
  foreach ($menu as $item) {
    $href = $item['type'] === 'internal'
      ? '/strona/' . htmlspecialchars($item['target'])
      : htmlspecialchars($item['target']);
    echo "<a href=\"$href\">" . htmlspecialchars($item['label']) . "</a> | ";
  }
  echo "</nav><hr>";
  echo $body;
  echo "<hr><footer><p>&copy; ".date('Y')." TataJestWazny</p></footer></body></html>";
}
?>
PHP

echo "✅ Menu zintegrowane i gotowe: http://tatajestwazny.local/admin/menu/"
