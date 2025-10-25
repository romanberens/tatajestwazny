#!/bin/bash
set -e

echo "🔧 Integracja panelu administracyjnego – START"

# 1. Tworzymy centralny auth.php
mkdir -p includes
cat > includes/auth.php <<'PHP'
<?php
session_start();

function auth() {
  if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /admin/auth.php');
    exit;
  }
}
PHP
echo "✅ includes/auth.php utworzony"

# 2. Tworzymy centralny dashboard: admin/index.php
cat > public/admin/index.php <<'PHP'
<?php
require_once __DIR__.'/../../includes/auth.php';
auth();

$title = "Panel administracyjny";
ob_start();
?>
<h1 class="text-xl font-semibold mb-4">Panel administracyjny</h1>
<ul class="space-y-2">
  <li><a href="/admin/pages/index.php" class="text-blue-600 hover:underline">📝 Podstrony</a></li>
  <li><a href="/admin/menu/index.php" class="text-blue-600 hover:underline">📋 Menu</a></li>
  <li><a href="/admin/blog/index.php" class="text-blue-600 hover:underline">✍️ Blog</a></li>
  <li><a href="/admin/reset_password.php" class="text-blue-600 hover:underline">🔐 Zmień hasło</a></li>
  <li><a href="/admin/auth.php?action=logout" class="text-red-600 hover:underline">🚪 Wyloguj się</a></li>
</ul>
<?php
$content = ob_get_clean();
include_once __DIR__.'/../../templates/admin_layout.php';
PHP
echo "✅ public/admin/index.php zaktualizowany (dashboard)"

# 3. Modyfikujemy auth.php, jeśli istnieje – nadpisujemy wersją z logiką login/logout
cat > public/admin/auth.php <<'PHP'
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['password'] === 'tajnehaslo') {
    $_SESSION['logged_in'] = true;
    header('Location: /admin/');
    exit;
  } else {
    $error = "Błędne hasło.";
  }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
  session_destroy();
  header('Location: /admin/auth.php');
  exit;
}
?>
<!DOCTYPE html><html lang="pl"><head>
<meta charset="UTF-8"><title>Logowanie</title>
<style>
  body { font-family: sans-serif; max-width: 400px; margin: 4em auto; }
  form { display: flex; flex-direction: column; gap: 1em; }
</style>
</head><body>
<h1>Zaloguj się do panelu</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <input type="password" name="password" placeholder="Hasło" required>
  <button type="submit">Zaloguj</button>
</form>
</body></html>
PHP
echo "✅ public/admin/auth.php (logowanie) zaktualizowane"

# 4. Usuwamy blog/auth.php jeśli istnieje
if [ -f public/admin/blog/auth.php ]; then
  rm public/admin/blog/auth.php
  echo "🗑️  public/admin/blog/auth.php usunięty"
fi

# 5. Patchujemy indexy: blog, menu, pages
PATCH_PATHS=(
  "public/admin/blog/index.php"
  "public/admin/menu/index.php"
  "public/admin/pages/index.php"
)

for file in "${PATCH_PATHS[@]}"; do
  if grep -q "require 'csrf.php'; auth();" "$file"; then
    sed -i '1i<?php require_once __DIR__"/../../../includes/auth.php"; auth(); ?>' "$file"
    echo "✅ $file — patched (auth)"
  elif grep -q "require_once '../auth.php';" "$file"; then
    sed -i 's|require_once \.\./auth\.php|require_once __DIR__."/../../../includes/auth.php"|g' "$file"
    echo "✅ $file — patched (require path)"
  fi
done

echo "✅ Integracja zakończona!"
