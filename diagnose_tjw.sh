#!/bin/bash
# 🔍 Diagnostyka projektu TataJestWazny
# Autor: Roman Berens – OneNetworks

LOG_FILE="/var/www/tatajestwazny/diagnose_tjw.log"
PROJECT_PATH="/var/www/tatajestwazny"
DB_PATH="$PROJECT_PATH/app/db/tjw.sqlite"

echo "🔍 Diagnoza aplikacji TataJestWazny" | tee $LOG_FILE
echo "📅 Data: $(date)" | tee -a $LOG_FILE
echo "----------------------------------------" | tee -a $LOG_FILE

# 1️⃣ Struktura katalogów
echo -e "\n📂 Struktura katalogów:" | tee -a $LOG_FILE
tree -L 3 $PROJECT_PATH 2>/dev/null | tee -a $LOG_FILE

# 2️⃣ Pliki kluczowe
echo -e "\n📦 Pliki kluczowe:" | tee -a $LOG_FILE
find $PROJECT_PATH -maxdepth 3 -type f \( -name "docker-compose.yml" -o -name "nginx.conf" -o -name "*.php" \) | tee -a $LOG_FILE

# 3️⃣ Baza danych SQLite
echo -e "\n🗄️  Baza danych SQLite:" | tee -a $LOG_FILE
if [ -f "$DB_PATH" ]; then
  ls -l $DB_PATH | tee -a $LOG_FILE
  stat $DB_PATH | tee -a $LOG_FILE
  echo -e "\n🔐 Uprawnienia katalogu db:" | tee -a $LOG_FILE
  ls -ld $(dirname $DB_PATH) | tee -a $LOG_FILE
else
  echo "❌ Plik bazy danych nie istnieje: $DB_PATH" | tee -a $LOG_FILE
fi

# 4️⃣ Procesy i kontenery
echo -e "\n🐳 Kontenery Dockera:" | tee -a $LOG_FILE
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep tjw | tee -a $LOG_FILE

echo -e "\n⚙️  Szczegóły sieci:" | tee -a $LOG_FILE
docker network ls | grep tjw | tee -a $LOG_FILE

echo -e "\n📡 Inspekcja traefik_net:" | tee -a $LOG_FILE
docker network inspect traefik_net --format '{{json .Containers}}' | jq | tee -a $LOG_FILE

# 5️⃣ Logi kontenerów
echo -e "\n📜 Logi kontenerów:" | tee -a $LOG_FILE
echo -e "\n🧩 Nginx:" | tee -a $LOG_FILE
docker logs tjw_nginx --tail=20 2>&1 | tee -a $LOG_FILE

echo -e "\n🧠 PHP-FPM:" | tee -a $LOG_FILE
docker logs tjw_php --tail=20 2>&1 | tee -a $LOG_FILE

# 6️⃣ Uprawnienia projektu
echo -e "\n🔍 Uprawnienia katalogów projektu:" | tee -a $LOG_FILE
find $PROJECT_PATH -maxdepth 3 -type d -exec ls -ld {} \; | tee -a $LOG_FILE

# 7️⃣ PHP info (wersja)
echo -e "\n💡 Test PHP info:" | tee -a $LOG_FILE
docker exec tjw_php php -v 2>&1 | tee -a $LOG_FILE

# 8️⃣ Automatyczna analiza
echo -e "\n🧠 Automatyczna analiza:" | tee -a $LOG_FILE
if [ -f "$DB_PATH" ]; then
  PERMS=$(stat -c "%a" $DB_PATH)
  OWNER=$(stat -c "%U" $DB_PATH)
  if [[ ("$PERMS" != "664" && "$PERMS" != "775" && "$PERMS" != "777") || "$OWNER" != "www-data" ]]; then
    echo "⚠️  Problem z bazą danych: niepoprawny właściciel lub uprawnienia." | tee -a $LOG_FILE
    echo "   Aktualne: $OWNER ($PERMS)" | tee -a $LOG_FILE
    echo "   Zalecane: chown -R 33:33 $PROJECT_PATH/app/db && chmod 775 -R $PROJECT_PATH/app/db" | tee -a $LOG_FILE
  else
    echo "✅ Uprawnienia bazy danych są prawidłowe ($OWNER:$PERMS)." | tee -a $LOG_FILE
  fi
else
  echo "⚠️  Nie znaleziono pliku bazy SQLite, aplikacja może nie działać." | tee -a $LOG_FILE
fi

echo -e "\n✅ Diagnostyka zakończona. Raport zapisano w: $LOG_FILE"
