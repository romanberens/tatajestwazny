#!/bin/bash

set -e

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
ADMIN_DIR="$BASE_DIR/public/admin"

echo "🔧 Tworzenie katalogu admin/..."
mkdir -p "$ADMIN_DIR"

echo "📄 Tworzenie plików panelu admin..."

# index.php - lista bloków
cat > "$ADMIN_DIR/index.php" <<'PHP'
<?php
require 'csrf.php';
auth();
$db = new PDO('sqlite:../../db/tjw.sqlite');
$blocks = $db->query("SELECT id, region, title FROM content_blocks ORDER BY region, position")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin</title></head><body>
<h1>📋 Bloki treści</h1><ul>
<?php foreach ($blocks as $b): ?>
  <li>[<?=htmlspecialchars($b['region'])?>] <strong><?=htmlspecialchars($b['title'])?></strong>
    — <a href="edit.php?id=<?=$b['id']?>">Edytuj</a>
  </li>
<?php endforeach; ?>
</ul></body></html>
PHP

# edit.php - formularz edycji
cat > "$ADMIN_DIR/edit.php" <<'PHP'
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
PHP

# save.php - zapis zmian
cat > "$ADMIN_DIR/save.php" <<'PHP'
<?php
require 'csrf.php';
auth();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Błąd metody.");
csrf_check($_POST['csrf'] ?? '');
$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$body_html = trim($_POST['body_html'] ?? '');
if (!$id || !$title || !$body_html) die("Brak danych.");
$db = new PDO('sqlite:../../db/tjw.sqlite');
$stmt = $db->prepare("UPDATE content_blocks SET title = ?, body_html = ?, updated_at = datetime('now') WHERE id = ?");
$stmt->execute([$title, $body_html, $id]);
header("Location: index.php");
exit;
PHP

# csrf.php - zabezpieczenia + Basic Auth
cat > "$ADMIN_DIR/csrf.php" <<'PHP'
<?php
session_start();

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_check($token) {
  if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
    die("❌ Niepoprawny token CSRF.");
  }
}

function auth() {
  $USER = 'admin';
  $PASS = 'tajnehaslo'; // 👈 ZMIEŃ TO PRZY WDROŻENIU

  if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
      $_SERVER['PHP_AUTH_USER'] !== $USER ||
      $_SERVER['PHP_AUTH_PW'] !== $PASS) {
    header('WWW-Authenticate: Basic realm="Admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo '❌ Dostęp zabroniony';
    exit;
  }
}
PHP

echo "✅ Pliki admin utworzone."

# Upewniamy się, że baza ma tabelę content_blocks
echo "🧱 Sprawdzanie tabeli content_blocks..."
sqlite3 "$BASE_DIR/db/tjw.sqlite" "
CREATE TABLE IF NOT EXISTS content_blocks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  region TEXT NOT NULL,
  position INTEGER NOT NULL,
  title TEXT NOT NULL,
  body_html TEXT NOT NULL,
  created_at TEXT NOT NULL DEFAULT (datetime('now')),
  updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX IF NOT EXISTS idx_blocks_region_pos ON content_blocks(region, position);
"

# Przykładowy wpis (jeśli pusty)
COUNT=$(sqlite3 "$BASE_DIR/db/tjw.sqlite" "SELECT COUNT(*) FROM content_blocks;")
if [ "$COUNT" -eq 0 ]; then
  echo "📥 Dodawanie przykładowych bloków..."
  sqlite3 "$BASE_DIR/db/tjw.sqlite" "
  INSERT INTO content_blocks(region,position,title,body_html)
  VALUES
    ('HOME',1,'Misja','<p>Nasza misja to...</p>'),
    ('HOME',2,'Jak pomagam','<ul><li>Darmowa pomoc</li></ul>'),
    ('HOME',3,'Dla kogo','<p>Dla każdego...</p>'),
    ('HOME',4,'Szybka pomoc','<p>Skontaktuj się...</p>');
  "
fi

echo "🏁 Panel admin gotowy: http://tatajestwazny.local/admin/"
