#!/bin/bash

set -e

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
DB="$BASE_DIR/db/tjw.sqlite"
ADMIN_DIR="$BASE_DIR/public/admin/pages"
INCLUDES_DIR="$BASE_DIR/public/includes"

echo "🧱 Tworzenie tabeli 'pages'..."
sqlite3 "$DB" '
CREATE TABLE IF NOT EXISTS pages (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  body_html TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);'

echo "📁 Tworzenie katalogu admin/pages/..."
mkdir -p "$ADMIN_DIR"
mkdir -p "$INCLUDES_DIR"

echo "🔗 Tworzenie csrf.php (jeśli brak)..."
[ -f "$BASE_DIR/public/admin/csrf.php" ] && cp "$BASE_DIR/public/admin/csrf.php" "$ADMIN_DIR/csrf.php"

echo "📄 Tworzenie plików panelu..."

# index.php - lista stron
cat > "$ADMIN_DIR/index.php" <<'PHP'
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
PHP

# add.php
cat > "$ADMIN_DIR/add.php" <<'PHP'
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
PHP

# edit.php
cat > "$ADMIN_DIR/edit.php" <<'PHP'
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
PHP

# save.php
cat > "$ADMIN_DIR/save.php" <<'PHP'
<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
csrf_check($_POST['csrf'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body_html'] ?? '');
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

if (!$slug || !$title || !$body) die("Brak danych.");

if ($id) {
  $stmt = $db->prepare("UPDATE pages SET slug=?, title=?, body_html=?, updated_at=datetime('now') WHERE id=?");
  $stmt->execute([$slug, $title, $body, $id]);
} else {
  $stmt = $db->prepare("INSERT INTO pages(slug, title, body_html) VALUES (?, ?, ?)");
  $stmt->execute([$slug, $title, $body]);
}
header("Location: index.php");
exit;
PHP

# delete.php
cat > "$ADMIN_DIR/delete.php" <<'PHP'
<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
exit;
PHP

# layout.php
cat > "$INCLUDES_DIR/layout.php" <<'PHP'
<?php
function layout($title, $body) {
  echo "<!doctype html><html><head><meta charset='utf-8'><title>$title</title></head><body>";
  echo "<nav><a href='/'>🏠 Strona główna</a> | <a href='/strona/jak-pomagam'>Jak pomagam</a></nav><hr>";
  echo $body;
  echo "<hr><footer><p>&copy; ".date('Y')." TataJestWazny</p></footer></body></html>";
}
?>
PHP

# Modyfikacja index.php — routing
INDEX_PHP="$BASE_DIR/public/index.php"
if ! grep -q '$slug = ' "$INDEX_PHP"; then
  echo "⚙️ Dodawanie routingu do index.php..."
  cat > "$INDEX_PHP" <<'PHP'
<?php
require __DIR__.'/includes/layout.php';
$db = new PDO('sqlite:../db/tjw.sqlite');

// Routing
$uri = $_SERVER['REQUEST_URI'];

if (preg_match('#^/strona/([a-z0-9\-]+)$#', $uri, $m)) {
  $slug = $m[1];
  $stmt = $db->prepare("SELECT title, body_html FROM pages WHERE slug = ?");
  $stmt->execute([$slug]);
  $page = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$page) {
    http_response_code(404);
    layout("404", "<h1>Nie znaleziono strony</h1>");
    exit;
  }
  layout($page['title'], $page['body_html']);
  exit;
}

// Domyślna strona
$blocks = $db->query("SELECT title, body_html FROM content_blocks ORDER BY region, position")->fetchAll(PDO::FETCH_ASSOC);
$body = '';
foreach ($blocks as $b) {
  $body .= "<h2>".htmlspecialchars($b['title'])."</h2>\n".$b['body_html']."\n";
}
layout("TataJestWazny", $body);
PHP
else
  echo "✔️ index.php już zawiera routing."
fi

echo "✅ Moduł podstron gotowy: http://tatajestwazny.local/admin/pages/"
