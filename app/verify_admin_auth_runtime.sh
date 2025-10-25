#!/bin/sh
set -e

# ===========================================
# 🔧 Detekcja środowiska (działa w Alpine)
# ===========================================
if [ -d "/var/www/html/app" ]; then
  APP="/var/www/html/app"        # w kontenerze
else
  APP="/var/www/tatajestwazny/app"  # na hoście
fi

PHP_INC="$APP/includes/load_env.php"
AUTH_PHP="$APP/public/admin/auth.php"
ENV="$APP/.env"
LOG="$APP/verify_admin_auth_runtime.log"

DOMAIN="${DOMAIN:-https://app.tatajestwazny.pl}"
PASS="${1:-}"

if [ -z "$PASS" ]; then
  printf "🔑 Podaj hasło do testu (to samo które wpisałeś na stronie): "
  read -r PASS
fi

# ===========================================
# 📜 Przygotowanie logu
# ===========================================
touch "$LOG" 2>/dev/null || true
chmod 666 "$LOG" 2>/dev/null || true

php_cmd() {
  if docker ps --format '{{.Names}}' 2>/dev/null | grep -q '^tjw_php$'; then
    docker exec tjw_php php "$@"
  else
    php "$@"
  fi
}

# ===========================================
# 🧪 Właściwa weryfikacja
# ===========================================
echo "🧪 Runtime weryfikacja logowania admina" | tee "$LOG"
echo "📅 $(date)" | tee -a "$LOG"
echo "----------------------------------------" | tee -a "$LOG"

echo "\n📂 Pliki:" | tee -a "$LOG"
for f in "$PHP_INC" "$AUTH_PHP" "$ENV"; do
  if [ -f "$f" ]; then
    echo "✅ $f" | tee -a "$LOG"
  else
    echo "❌ Brak: $f" | tee -a "$LOG"
  fi
done

echo "\n🧩 Odczyt AUTH_PASSWORD_HASH z runtime (po require load_env.php):" | tee -a "$LOG"
php -r "
error_reporting(E_ALL);
require '$PHP_INC';
\$h = \$_ENV['AUTH_PASSWORD_HASH'] ?? null;
echo \$h ? (\"HASH=\".\$h.\"\\n\") : \"HASH=(nie ustawiony)\\n\";" | tee -a "$LOG"

echo "\n🔐 Walidacja bcrypt + password_verify:" | tee -a "$LOG"
php -r "
require '$PHP_INC';
\$h = \$_ENV['AUTH_PASSWORD_HASH'] ?? '';
\$is_bcrypt = (bool)preg_match('/^\\$2[aby]?\\$\\d{2}\\$/', \$h);
echo 'Format bcrypt: ' . (\$is_bcrypt ? 'TAK' : 'NIE') . \"\\n\";
\$ok = (\$h && password_verify('$PASS', \$h));
echo 'password_verify: ' . (\$ok ? 'OK' : 'FAIL') . \"\\n\";" | tee -a "$LOG"

echo "\n✅ Log zapisany w: $LOG"
