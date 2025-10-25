#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="/var/www/tatajestwazny/app"
ENV_FILE="$APP_DIR/.env"
ADMIN_AUTH_PHP="$APP_DIR/public/admin/auth.php"
LOAD_ENV_PHP="$APP_DIR/includes/load_env.php"
LOG="$APP_DIR/admin_password_report.log"

# Użyj PHP z kontenera (jeśli jest), w innym wypadku lokalnego
php_cmd() {
  if docker ps --format '{{.Names}}' | grep -q '^tjw_php$'; then
    docker exec tjw_php php "$@"
  else
    php "$@"
  fi
}

echo "🔍 Raport konfiguracji hasła admina (TataJestWazny)" | tee "$LOG"
echo "📅 $(date)" | tee -a "$LOG"
echo "----------------------------------------" | tee -a "$LOG"

echo -e "\n📌 Miejsce przechowywania hasła:" | tee -a "$LOG"
if [[ -f "$ENV_FILE" ]] && grep -q '^AUTH_PASSWORD_HASH=' "$ENV_FILE"; then
  HASH_LINE=$(grep '^AUTH_PASSWORD_HASH=' "$ENV_FILE" | head -n1)
  echo "✅ Znaleziono hash w $ENV_FILE:" | tee -a "$LOG"
  echo "   $HASH_LINE" | tee -a "$LOG"
else
  echo "⚠️  Brak wpisu AUTH_PASSWORD_HASH w $ENV_FILE" | tee -a "$LOG"
fi

echo -e "\n🧠 Miejsce użycia / weryfikacji hasła:" | tee -a "$LOG"
if [[ -f "$ADMIN_AUTH_PHP" ]]; then
  echo "• $ADMIN_AUTH_PHP" | tee -a "$LOG"
  grep -nE 'password_verify|AUTH_PASSWORD_HASH' "$ADMIN_AUTH_PHP" | sed 's/^/   /' | tee -a "$LOG" || true
else
  echo "❌ Brak pliku $ADMIN_AUTH_PHP" | tee -a "$LOG"
fi

echo -e "\n🧩 Sprawdzanie wczytywania .env do \$_ENV:" | tee -a "$LOG"
if [[ -f "$LOAD_ENV_PHP" ]]; then
  echo "• Parser .env: $LOAD_ENV_PHP" | tee -a "$LOG"
else
  echo "❌ Brak $LOAD_ENV_PHP — bez tego \$_ENV może być puste" | tee -a "$LOG"
fi

# Upewnij się, że load_env.php jest dołączony zanim czytamy AUTH_PASSWORD_HASH
if [[ -f "$ADMIN_AUTH_PHP" ]]; then
  if ! grep -q "includes/load_env.php" "$ADMIN_AUTH_PHP"; then
    # wstrzyknij require tuż po otwarciu PHP, ale tylko jeśli jeszcze go nie ma
    sed -i '0,/<?php/s##<?php\nrequire_once __DIR__ . "\/\/..\/..\/includes\/load_env.php";#' "$ADMIN_AUTH_PHP"
    echo "🔧 Dodano require_once includes/load_env.php do $ADMIN_AUTH_PHP" | tee -a "$LOG"
  else
    echo "✅ $ADMIN_AUTH_PHP już ładuje includes/load_env.php" | tee -a "$LOG"
  fi
fi

# ------ USTAWIENIE / ZMIANA HASŁA ------

PASSWORD="${1:-}"   # opcjonalnie: ./explain_and_set_admin_password.sh 'NoweHaslo123!'
if [[ -z "${PASSWORD}" ]]; then
  read -rsp "🔑 Podaj nowe (startowe) hasło admina: " PASSWORD
  echo
fi
if [[ -z "${PASSWORD}" ]]; then
  echo "❌ Nie podano hasła. Przerywam."
  exit 1
fi

HASH=$(php_cmd -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "$PASSWORD")

mkdir -p "$(dirname "$ENV_FILE")"
touch "$ENV_FILE"
if grep -q '^AUTH_PASSWORD_HASH=' "$ENV_FILE"; then
  sed -i "s|^AUTH_PASSWORD_HASH=.*|AUTH_PASSWORD_HASH=\"$HASH\"|" "$ENV_FILE"
else
  printf 'AUTH_PASSWORD_HASH="%s"\n' "$HASH" >> "$ENV_FILE"
fi
echo "✅ Zapisano hash do $ENV_FILE" | tee -a "$LOG"

# Szybki autotest password_verify
VERIFY=$(php_cmd -r '[$p,$h]=[$argv[1],$argv[2]]; echo password_verify($p,$h) ? "OK" : "FAIL";' "$PASSWORD" "$HASH")
echo "🧪 Test password_verify (na świeżym hashu): $VERIFY" | tee -a "$LOG"

cat <<TXT | tee -a "$LOG"

ℹ️ Podsumowanie:
- Hasło admina jest trzymane jako bcrypt w: $ENV_FILE, klucz: AUTH_PASSWORD_HASH
- Podczas logowania używany jest: $ADMIN_AUTH_PHP (password_verify na AUTH_PASSWORD_HASH)
- .env musi być wczytany przez: $LOAD_ENV_PHP (dopiąłem, jeśli brakowało)

▶️ Co dalej:
1) Wejdź na: https://app.tatajestwazny.pl/admin/auth.php
2) Zaloguj się hasłem ustawionym przed chwilą.

(Jeśli przeglądarka trzymała stare sesje/cookies — odśwież twardo lub wyczyść cookies dla domeny.)
TXT

echo "📄 Pełny raport: $LOG"
