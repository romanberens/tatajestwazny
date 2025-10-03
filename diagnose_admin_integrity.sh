#!/bin/bash
set -e

echo "🔍 Diagnoza integracji panelu admina z blogiem"

echo ""
echo "📁 Struktura katalogu głównego:"
tree -L 2 -I 'node_modules|vendor|.git' .

echo ""
echo "📂 Sprawdzam obecność katalogów i plików krytycznych:"
for path in "includes/auth.php" "public/admin/index.php" "public/admin/blog/index.php" "public/includes/layout.php" "db/tjw.sqlite"; do
  if [ -f "$path" ]; then
    echo "✅ $path istnieje"
  else
    echo "❌ $path BRAKUJE"
  fi
done

echo ""
echo "🧪 Testuję odpowiedzi HTTP (curl):"

URLS=(
  "http://tatajestwazny.local/"
  "http://tatajestwazny.local/blog"
  "http://tatajestwazny.local/blog/hello"
  "http://tatajestwazny.local/admin/"
  "http://tatajestwazny.local/admin/blog/"
  "http://tatajestwazny.local/admin/login"
)

for url in "${URLS[@]}"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "$url")
  echo "🔗 $url → HTTP $code"
done

echo ""
echo "🧠 Struktura bazy SQLite:"
if [ -f db/tjw.sqlite ]; then
  sqlite3 db/tjw.sqlite ".tables"
else
  echo "❌ Brak bazy danych db/tjw.sqlite"
fi

echo ""
echo "👀 Sprawdzam logikę auth:"
grep -Ri --include="*.php" "session_start" . | cut -c 1-140
grep -Ri --include="*.php" "require.*auth" . | cut -c 1-140
