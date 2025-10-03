#!/bin/bash
# 🔍 FULL TEST AUTORYZACJI I DIAGNOSTYKI BŁĘDÓW 500
set -e

URL="http://localhost/admin/blog/index.php"
COOKIE_FILE="cookies.txt"
PASSWORD="MuremZaOjcami.1"

echo "🔍 FULL TEST AUTORYZACJI I DIAGNOZY BŁĘDU 500"
echo "==========================================="

echo "📦 1. Sprawdzanie pliku .env..."
HASH_LINE=$(grep AUTH_PASSWORD_HASH .env || echo "❌ Brak zmiennej AUTH_PASSWORD_HASH")
echo "$HASH_LINE"

echo ""
echo "🔐 2. Test password_verify() w PHP..."
php -r "
\$hash = getenv('AUTH_PASSWORD_HASH');
if (!\$hash) {
    \$lines = file('.env');
    foreach (\$lines as \$line) {
        if (strpos(\$line, 'AUTH_PASSWORD_HASH=') === 0) {
            \$hash = trim(explode('=', \$line, 2)[1], \" \\\"\");
        }
    }
}
if (password_verify('$PASSWORD', \$hash)) {
    echo \"✅ Hasło poprawne\n\";
} else {
    echo \"❌ Hasło NIE PASUJE\n\";
}
"

echo ""
echo "🌐 3. Test logowania curl..."
curl -s -i -X POST "$URL" \
  -c "$COOKIE_FILE" \
  -d "password=$PASSWORD" | tee /tmp/response_headers.txt

if grep -q "500 Internal Server Error" /tmp/response_headers.txt; then
  echo "❌ Błąd 500 – wewnętrzny błąd serwera"
else
  echo "✅ Brak błędu 500 przy logowaniu"
fi

echo ""
echo "🍪 4. Sprawdzenie sesji (czy widzimy panel, nie formularz)..."
curl -s "$URL" -b "$COOKIE_FILE" > /tmp/panel.html

if grep -q "Zaloguj" /tmp/panel.html; then
  echo "❌ Nadal formularz logowania – sesja nie działa"
else
  echo "✅ Panel widoczny – sesja aktywna"
fi

echo ""
echo "🔁 5. Sprawdzenie obecności include 'auth.php'..."
find public/admin/blog -type f -name '*.php' | while read -r file; do
  if grep -q "auth.php" "$file"; then
    echo "✅ $file zawiera auth.php"
  else
    echo "⚠️  $file NIE zawiera auth.php"
  fi
done

echo ""
echo "🧠 6. Diagnostyka błędu 500 (logi)..."
ERROR_LOG="/var/log/nginx/error.log"
if [[ -f $ERROR_LOG ]]; then
  tail -n 10 "$ERROR_LOG"
else
  echo "⚠️ Nie znaleziono pliku logów Nginx: $ERROR_LOG"
fi

echo ""
echo "✅ TESTY ZAKOŃCZONE"
