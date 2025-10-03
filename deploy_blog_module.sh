#!/bin/bash
set -e

# Zakładamy, że skrypt jest uruchamiany z katalogu głównego projektu
BASE_DIR="$(pwd)"
DB="$BASE_DIR/db/tjw.sqlite"
ADMIN_DIR="$BASE_DIR/public/admin/blog"
INCLUDES_DIR="$BASE_DIR/public/includes"
INDEX_PHP="$BASE_DIR/public/index.php"

echo "📁 Tworzenie katalogu admin/blog..."
mkdir -p "$ADMIN_DIR"

# Sprawdzenie czy baza istnieje
if [ ! -f "$DB" ]; then
  echo "❌ Baza danych nie istnieje: $DB"
  exit 1
fi

echo "📦 Tworzenie tabeli posts..."
sqlite3 "$DB" '
CREATE TABLE IF NOT EXISTS posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  excerpt TEXT,
  body_html TEXT NOT NULL,
  published_at TEXT,
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);'

echo "🔗 Kopiowanie csrf.php do admin/blog..."
[ -f "$BASE_DIR/public/admin/csrf.php" ] && cp "$BASE_DIR/public/admin/csrf.php" "$ADMIN_DIR/csrf.php"

echo "⚙️ Sprawdzanie routingu w public/index.php..."

if ! grep -q '/blog' "$INDEX_PHP"; then
  echo "🧩 Dodawanie obsługi /blog i /blog/<slug> do index.php"
  cat > "$INDEX_PHP" <<'PHP'
<?php
require __DIR__.'/includes/layout.php';
$db = new PDO('sqlite:../db/tjw.sqlite');

$uri = $_SERVER['REQUEST_URI'];

// 📄 /blog/<slug>
if (preg_match('#^/blog/([a-z0-9\-]+)$#', $uri, $m)) {
  $slug = $m[1];
  $stmt = $db->prepare("SELECT title, body_html FROM posts WHERE slug = ? AND published_at IS NOT NULL");
  $stmt->execute([$slug]);
  $post = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$post) {
    http_response_code(404);
    layout("404", "<h1>Nie znaleziono wpisu</h1>");
    exit;
  }
  layout($post['title'], $post['body_html']);
  exit;
}

// 🗂️ /blog (lista)
if ($uri === '/blog') {
  $posts = $db->query("SELECT slug, title, excerpt, published_at FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC")->fetchAll();
  $html = "<h1>📚 Blog</h1><ul>";
  foreach ($posts as $p) {
    $html .= "<li><a href='/blog/" . htmlspecialchars($p['slug']) . "'><strong>" . htmlspecialchars($p['title']) . "</strong></a>";
    if ($p['excerpt']) $html .= "<p>" . htmlspecialchars($p['excerpt']) . "</p>";
    $html .= "</li>";
  }
  $html .= "</ul>";
  layout("Blog", $html);
  exit;
}

// 🏠 domyślnie: strona główna (bloki)
$blocks = $db->query("SELECT title, body_html FROM content_blocks ORDER BY region, position")->fetchAll();
$body = '';
foreach ($blocks as $b) {
  $body .= "<h2>".htmlspecialchars($b['title'])."</h2>\n".$b['body_html']."\n";
}
layout("TataJestWazny", $body);
PHP
else
  echo "✔️ Routing bloga już istnieje – pomijam modyfikację index.php"
fi

echo "✅ Routing gotowy. Teraz możesz dodać pliki panelu admin/blog/"
