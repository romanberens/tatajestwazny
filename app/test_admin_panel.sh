#!/bin/bash

# CONFIG
BASE_URL="http://tatajestwazny.local"
ADMIN_PASSWORD="tajnehaslo"
COOKIE_FILE="cookies.txt"
NGINX_LOG="/var/log/nginx/error.log"
PHP_LOG="/var/log/php8.2-fpm.log"  # zmień na swoją wersję PHP

# 🔁 Restart usług (jeśli masz prawa root)
echo "🔁 Restartuję Nginx i PHP-FPM..."
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# 🧹 Czyszczenie logów
echo "🧹 Czyszczę logi..."
sudo truncate -s 0 "$NGINX_LOG"
sudo truncate -s 0 "$PHP_LOG"

# 🔐 Logowanie do panelu admina
echo "🔐 Logowanie..."
curl -s -c "$COOKIE_FILE" -d "password=$ADMIN_PASSWORD" "$BASE_URL/admin/auth.php" > /dev/null

# ✅ Testowane endpointy
ENDPOINTS=(
  "/admin/"
  "/admin/blog/index.php"
  "/admin/pages/index.php"
  "/admin/menu/index.php"
  "/admin/save.php"
)

echo "🧪 Testowanie endpointów:"
for ep in "${ENDPOINTS[@]}"; do
  echo -n "→ $ep ... "
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_FILE" "$BASE_URL$ep")
  if [ "$STATUS" = "200" ]; then
    echo "✅ OK (HTTP $STATUS)"
  else
    echo "❌ Błąd (HTTP $STATUS)"
  fi
done

# 📋 Podgląd błędów
echo -e "\n📋 Logi Nginx (jeśli są):"
sudo tail -n 10 "$NGINX_LOG"

echo -e "\n📋 Logi PHP-FPM (jeśli są):"
sudo tail -n 10 "$PHP_LOG"

echo -e "\n🎉 Test zakończony. Jeśli wszystko było OK — panel działa poprawnie."
