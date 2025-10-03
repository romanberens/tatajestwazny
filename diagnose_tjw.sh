#!/bin/bash
echo "🔍 Diagnoza aplikacji tatajestwazny"
echo "==================================="

ROOT_DIR="$(pwd)"
MAIN_PHP="$ROOT_DIR/public/index.php"
ADMIN_PHP="$ROOT_DIR/public/admin/index.php"
BLOG_PHP="$ROOT_DIR/public/admin/blog/index.php"
LAYOUT_PHP="$ROOT_DIR/public/includes/layout.php"

echo
echo "📦 Sprawdzanie kluczowych plików PHP:"

# 1. Główna strona
if [ -f "$MAIN_PHP" ]; then
    echo "✅ Znaleziono: public/index.php"
    ERR=$(php -l "$MAIN_PHP" 2>&1)
    if echo "$ERR" | grep -q "Parse error"; then
        echo "   ❌ Błąd składni: $ERR"
    else
        echo "   ✅ Składnia poprawna"
    fi
else
    echo "❌ Brak pliku public/index.php"
fi

# 2. Plik layout.php (częsty błąd)
if [ -f "$LAYOUT_PHP" ]; then
    echo "✅ Znaleziono: public/includes/layout.php"
    ERR=$(php -l "$LAYOUT_PHP" 2>&1)
    if echo "$ERR" | grep -q "Parse error"; then
        echo "   ❌ Błąd składni: $ERR"
    else
        echo "   ✅ Składnia poprawna"
    fi
else
    echo "❌ Brak pliku public/includes/layout.php"
fi

# 3. Admin index.php
if [ -f "$ADMIN_PHP" ]; then
    echo "✅ Znaleziono: public/admin/index.php"
    php -l "$ADMIN_PHP" >/dev/null && echo "   ✅ Plik PHP działa poprawnie" || echo "   ❌ Błąd składni w admin/index.php"
else
    echo "❌ Brak pliku public/admin/index.php"
fi

# 4. Blog index.php
if [ -f "$BLOG_PHP" ]; then
    echo "✅ Znaleziono: public/admin/blog/index.php"
    ERR=$(php "$BLOG_PHP" 2>&1)
    if echo "$ERR" | grep -q "Fatal error"; then
        echo "   ❌ Błąd uruchomienia: $ERR"
    else
        echo "   ✅ Plik PHP działa poprawnie"
    fi
else
    echo "❌ Brak pliku public/admin/blog/index.php"
fi

echo
echo "🌍 Testowanie HTTP przez curl (jeśli nginx działa):"
for url in "/" "/admin/" "/admin/blog/"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" http://localhost$url)
    echo "🔗 http://localhost$url -> $code"
done

echo
echo "🔐 Sprawdzanie pliku .env:"
if [ -f ".env" ]; then
    echo "✅ .env istnieje"
    grep AUTH_PASSWORD_HASH .env || echo "⚠️  AUTH_PASSWORD_HASH nie znaleziony"
else
    echo "❌ Brak pliku .env"
fi

echo
echo "🪵 Logi PHP-FPM:"
LOG_FILE=$(find /var/log -type f -name "*fpm*.log" 2>/dev/null | head -n 1)
if [ -n "$LOG_FILE" ]; then
    sudo tail -n 20 "$LOG_FILE"
else
    echo "❌ Nie znaleziono logu PHP-FPM"
fi

echo
echo "✅ Diagnoza zakończona"
