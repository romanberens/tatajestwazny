#!/bin/bash
# 🔍 diagnose_cms.sh — kompleksowa diagnoza CMS tatajestwazny.pl
# Roman Berens • OneNetworks • 2025-10-24

set -euo pipefail

REPORT="cms_diagnostics_$(date +%Y%m%d_%H%M%S).log"
APP_DIR="/var/www/tatajestwazny/app"
PHP_CONTAINER="tjw_php"
DB_PATH="$APP_DIR/db/tjw.sqlite"

echo "📋 Raport diagnostyczny CMS ($REPORT)"
echo "Czas: $(date)" | tee "$REPORT"
echo "=========================================" | tee -a "$REPORT"

# --- Podstawy środowiska ---
echo -e "\n## 🧩 Informacje systemowe" | tee -a "$REPORT"
uname -a | tee -a "$REPORT"
lsb_release -a 2>/dev/null | tee -a "$REPORT" || true

echo -e "\n## 📦 Docker i kontenery" | tee -a "$REPORT"
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | tee -a "$REPORT"

echo -e "\n## ⚙️ PHP w kontenerze ($PHP_CONTAINER)" | tee -a "$REPORT"
docker exec "$PHP_CONTAINER" php -v 2>&1 | tee -a "$REPORT"
docker exec "$PHP_CONTAINER" php -m | grep -E "pdo|sqlite|curl|mbstring" | tee -a "$REPORT"

# --- Struktura projektu ---
echo -e "\n## 📂 Struktura katalogów aplikacji" | tee -a "$REPORT"
tree -L 3 "$APP_DIR" | tee -a "$REPORT" || ls -R "$APP_DIR" | tee -a "$REPORT"

echo -e "\n## 🔑 Uprawnienia plików kluczowych" | tee -a "$REPORT"
ls -l "$APP_DIR"/includes/auth.php "$APP_DIR"/src/*.php "$APP_DIR"/public/admin/index.php 2>/dev/null | tee -a "$REPORT"

# --- Konfiguracja środowiska ---
echo -e "\n## 🔧 Zawartość .env (bez haseł)" | tee -a "$REPORT"
grep -vE 'AUTH_PASSWORD_HASH|PASSWORD|SECRET' "$APP_DIR/.env" 2>/dev/null | tee -a "$REPORT" || echo "Brak pliku .env" | tee -a "$REPORT"

# --- Baza danych ---
echo -e "\n## 🗃️ Baza danych SQLite" | tee -a "$REPORT"
if [ -f "$DB_PATH" ]; then
  ls -lh "$DB_PATH" | tee -a "$REPORT"
  sqlite3 "$DB_PATH" "SELECT name FROM sqlite_master WHERE type='table';" | tee -a "$REPORT"
  echo -e "\nPrzykładowe kolumny z tabel 'pages' i 'blocks':" | tee -a "$REPORT"
  sqlite3 "$DB_PATH" "PRAGMA table_info(pages);" 2>/dev/null | tee -a "$REPORT"
  sqlite3 "$DB_PATH" "PRAGMA table_info(blocks);" 2>/dev/null | tee -a "$REPORT"
else
  echo "❌ Brak pliku bazy danych: $DB_PATH" | tee -a "$REPORT"
fi

# --- Test dostępu PHP do bazy ---
echo -e "\n## 🧪 Test połączenia PDO w kontenerze PHP" | tee -a "$REPORT"
docker exec "$PHP_CONTAINER" php -r "
try {
  \$pdo = new PDO('sqlite:/var/www/html/app/db/tjw.sqlite');
  echo \"PDO SQLite: OK\n\";
  \$tables = \$pdo->query(\"SELECT name FROM sqlite_master WHERE type='table'\")->fetchAll(PDO::FETCH_COLUMN);
  echo 'Tabele: ' . implode(', ', \$tables) . \"\n\";
} catch (Exception \$e) {
  echo '❌ PDO error: ' . \$e->getMessage() . \"\n\";
}
" 2>&1 | tee -a "$REPORT"

# --- Moduły aplikacji ---
echo -e "\n## 📜 Wykryte funkcje obsługi POST" | tee -a "$REPORT"
grep -R "function handle" "$APP_DIR/src/" | tee -a "$REPORT"

echo -e "\n## 🧠 Zależności w includes/" | tee -a "$REPORT"
grep -E "require|include" "$APP_DIR/public/admin/index.php" | tee -a "$REPORT"

# --- Sprawdzenie błędów PHP ---
echo -e "\n## ⚠️ Ostatnie błędy PHP (z kontenera)" | tee -a "$REPORT"
docker logs "$PHP_CONTAINER" --tail=50 | tee -a "$REPORT"

echo -e "\n✅ Raport zakończony pomyślnie."
echo "Zapisano do: $PWD/$REPORT"
