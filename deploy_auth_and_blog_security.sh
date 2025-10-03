#!/bin/bash
set -e

echo "🔐 Wdrażanie autoryzacji i funkcji bezpieczeństwa..."

BASE_DIR="$(cd "$(dirname "$0")" && pwd)"
ADMIN_DIR="$BASE_DIR/public/admin/blog"
STYLE_FILE="$BASE_DIR/public/style.css"
AUTH_FILE="$BASE_DIR/public/auth.php"
DELETE_FILE="$ADMIN_DIR/delete.php"
LAYOUT_FILE="$BASE_DIR/public/includes/layout.php"
ENV_FILE="$BASE_DIR/.env"

mkdir -p "$ADMIN_DIR"

### 1. Generuj hash hasła, jeśli .env nie istnieje
if [ ! -f "$ENV_FILE" ]; then
  echo "🔑 Podaj hasło do panelu administracyjnego:"
  read -s PASSWORD
  HASH=$(php -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);")
  echo "AUTH_PASSWORD_HASH='$HASH'" > "$ENV_FILE"
  echo "✅ Utworzono plik .env z hashem hasła"
else
  echo "ℹ️ Plik .env już istnieje, nie modyfikuję"
fi

### 2. auth.php
cat > "$AUTH_FILE" <<'PHP'
<?php
session_start();
$hash = getenv('AUTH_PASSWORD_HASH') ?: (getenv('AUTH_PASSWORD_HASH') ?? '');

if (!$hash) {
  die('❌ Brakuje zmiennej środowiskowej AUTH_PASSWORD_HASH');
}

if (!isset($_SESSION['logged_in'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (password_verify($_POST['password'], $hash)) {
      $_SESSION['logged_in'] = true;
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    } else {
      $error = 'Nieprawidłowe hasło!';
    }
  }

  echo '<form method="post"><h2>🔐 Logowanie</h2>';
  if (isset($error)) echo '<p style="color:red">'.$error.'</p>';
  echo '<input type="password" name="password" placeholder="Hasło">';
  echo '<button type="submit">Zaloguj</button></form>';
  exit;
}
?>
PHP

echo "✅ Stworzono auth.php"

### 3. delete.php
cat > "$DELETE_FILE" <<'PHP'
<?php
require_once '../auth.php';

$db = new PDO('sqlite:../../db/tjw.sqlite');

$slug = $_GET['slug'] ?? '';
if ($slug) {
  $stmt = $db->prepare("DELETE FROM posts WHERE slug = ?");
  $stmt->execute([$slug]);
}

header('Location: index.php');
exit;
?>
PHP

echo "✅ Stworzono delete.php"

### 4. style.css
cat > "$STYLE_FILE" <<'CSS'
body {
  font-family: sans-serif;
  max-width: 700px;
  margin: 2rem auto;
  padding: 0 1rem;
  background: #fff;
  color: #111;
}
nav {
  margin-bottom: 1rem;
}
nav a {
  margin-right: 1rem;
  text-decoration: none;
  color: #0070f3;
}
nav a:hover {
  text-decoration: underline;
}
footer {
  margin-top: 2rem;
  font-size: 0.8em;
  color: #666;
}
CSS

echo "✅ Dodano style.css"

### 5. Dodaj <link> do layout.php jeśli nie istnieje
if ! grep -q "style.css" "$LAYOUT_FILE"; then
  sed -i "/<head>/a <link rel=\"stylesheet\" href=\"/style.css\">" "$LAYOUT_FILE"
  echo "✅ Dodano <link> do style.css w layout.php"
else
  echo "ℹ️ layout.php już zawiera <link> do CSS"
fi

echo ""
echo "🎉 GOTOWE!"
echo "👉 Upewnij się, że zmienna AUTH_PASSWORD_HASH jest załadowana:"
echo "   source ~/.bashrc  (lub użyj direnv, lub systemd dla serwera)"
echo "   Wartość znajduje się w: $ENV_FILE"
