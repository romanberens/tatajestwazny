#!/bin/bash

echo "🔐 Wdrażanie autoryzacji i funkcji bezpieczeństwa..."

read -sp "🔑 Podaj hasło do panelu administracyjnego: " PASSWORD
echo

HASH=$(php -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);")
echo "AUTH_PASSWORD_HASH=\"$HASH\"" > .env
echo "✅ Utworzono plik .env z hashem hasła"

# Plik includes/load_env.php
mkdir -p includes
cat > includes/load_env.php <<EOF
<?php
\$envPath = dirname(__DIR__) . '/.env';
if (file_exists(\$envPath)) {
  \$lines = file(\$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach (\$lines as \$line) {
    if (strpos(trim(\$line), '#') === 0) continue;
    list(\$key, \$value) = explode('=', \$line, 2);
    putenv(trim(\$key) . '=' . trim(\$value));
  }
}
?>
EOF
echo "✅ Stworzono includes/load_env.php"

# auth.php
cat > public/admin/auth.php <<EOF
<?php
session_start();
require_once __DIR__ . '/../../includes/load_env.php';

\$storedHash = getenv('AUTH_PASSWORD_HASH');

if (!isset(\$_SESSION['logged_in'])) {
  if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    if (password_verify(\$_POST['password'], \$storedHash)) {
      \$_SESSION['logged_in'] = true;
      header('Location: index.php');
      exit;
    } else {
      \$error = 'Nieprawidłowe hasło!';
    }
  }

  echo '<form method="post"><h2>🔐 Logowanie</h2>';
  if (isset(\$error)) echo '<p style="color:red">'.\$error.'</p>';
  echo '<input type="password" name="password" placeholder="Hasło">';
  echo '<button type="submit">Zaloguj</button></form>';
  exit;
}
?>
EOF
echo "✅ Stworzono auth.php"

# delete.php
cat > public/admin/blog/delete.php <<EOF
<?php
require_once '../auth.php';
\$db = new PDO('sqlite:../../db/tjw.sqlite');

\$slug = \$_GET['slug'] ?? '';
if (\$slug) {
  \$stmt = \$db->prepare("DELETE FROM posts WHERE slug = ?");
  \$stmt->execute([\$slug]);
}

header('Location: index.php');
exit;
?>
EOF
echo "✅ Stworzono delete.php"

# style.css
cat > public/style.css <<EOF
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
EOF
echo "✅ Dodano style.css"

# Dodanie <link> do layout.php
LAYOUT="templates/layout.php"
if ! grep -q "style.css" "\$LAYOUT"; then
  sed -i "/<head>/a <link rel='stylesheet' href='/style.css'>" "\$LAYOUT"
  echo "✅ Dodano <link> do style.css w layout.php"
else
  echo "ℹ️  Styl już załadowany w layout.php"
fi

echo
echo "🎉 GOTOWE!"
echo "👉 Upewnij się, że zmienna AUTH_PASSWORD_HASH jest załadowana:"
echo "   source ~/.bashrc (lub użyj direnv, lub systemd dla serwera)"
echo "   Wartość znajduje się w: $PWD/.env"
