#!/bin/bash
# 🔍 Analiza mechanizmu autoryzacji w projekcie Tata Jest Ważny
# Autor: Roman Berens — OneNetworks

APP_PATH="/var/www/tatajestwazny/app"
LOG_FILE="$APP_PATH/auth_analysis.log"

echo "🔍 Analiza autoryzacji (Tata Jest Ważny)" | tee $LOG_FILE
echo "📅 Data: $(date)" | tee -a $LOG_FILE
echo "----------------------------------------" | tee -a $LOG_FILE

# 1️⃣ Szukanie plików odpowiedzialnych za logowanie
echo -e "\n📂 Pliki logowania (auth.php, login.php itp.):" | tee -a $LOG_FILE
find "$APP_PATH" -type f -iname "*auth*.php" -o -iname "*login*.php" | tee -a $LOG_FILE

# 2️⃣ Szukanie wystąpień haseł i funkcji uwierzytelniania
echo -e "\n🧩 Wykrywanie funkcji i zmiennych powiązanych z hasłem:" | tee -a $LOG_FILE
grep -Rin --color=never -E "password|PASSWORD|verify|hash|bcrypt|login|auth" "$APP_PATH" | tee -a $LOG_FILE

# 3️⃣ Weryfikacja, czy istnieje plik konfiguracyjny z hasłem
echo -e "\n⚙️ Sprawdzanie pliku konfiguracyjnego config.php:" | tee -a $LOG_FILE
if [ -f "$APP_PATH/config/config.php" ]; then
  grep -E "password|pass" "$APP_PATH/config/config.php" | tee -a $LOG_FILE
else
  echo "❌ Brak pliku config.php" | tee -a $LOG_FILE
fi

# 4️⃣ Sprawdzenie bazy SQLite
DB_FILE="$APP_PATH/db/tjw.sqlite"
if [ -f "$DB_FILE" ]; then
  echo -e "\n🗄️ Analiza bazy danych SQLite:" | tee -a $LOG_FILE
  docker exec tjw_php sqlite3 /var/www/html/app/db/tjw.sqlite ".tables" | tee -a $LOG_FILE
  docker exec tjw_php sqlite3 /var/www/html/app/db/tjw.sqlite "SELECT name FROM sqlite_master WHERE sql LIKE '%password%';" | tee -a $LOG_FILE
else
  echo -e "\n⚠️ Brak pliku bazy danych $DB_FILE" | tee -a $LOG_FILE
fi

# 5️⃣ Szukanie użycia funkcji password_verify() lub password_hash()
echo -e "\n🔍 Szukanie funkcji password_verify / password_hash:" | tee -a $LOG_FILE
grep -Rin "password_verify" "$APP_PATH" | tee -a $LOG_FILE
grep -Rin "password_hash" "$APP_PATH" | tee -a $LOG_FILE

# 6️⃣ Analiza includes/auth.php
AUTH_FILE="$APP_PATH/includes/auth.php"
if [ -f "$AUTH_FILE" ]; then
  echo -e "\n📜 Fragment pliku auth.php (pierwsze 40 linii):" | tee -a $LOG_FILE
  head -n 40 "$AUTH_FILE" | tee -a $LOG_FILE
else
  echo -e "\n⚠️ Brak pliku includes/auth.php" | tee -a $LOG_FILE
fi

echo -e "\n✅ Analiza zakończona. Raport zapisano w: $LOG_FILE"
echo "Aby wyświetlić: cat $LOG_FILE"
