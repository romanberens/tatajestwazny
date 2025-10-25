#!/bin/sh
# 🌐 test_api_post.sh
# End-to-end test API panelu admina – symulacja POST do /admin/?section=pages

BASE_URL="http://app.tatajestwazny.pl"
TMP_COOKIE="/tmp/tjw_cookies.txt"
LOGFILE="/var/www/html/app/logs/diagnostics/cms_api_post_$(date +%Y%m%d_%H%M%S).log"

log() {
  echo "[$(date '+%F %T')] $1" | tee -a "$LOGFILE"
}

log "=== 🌐 START API POST TEST ==="

# 1️⃣ Pobierz token CSRF (zgodny z BusyBox / Alpine)
CSRF_TOKEN=$(curl -s -c "$TMP_COOKIE" "$BASE_URL/admin/?section=pages" \
  | sed -n 's/.*name="csrf" value="\([^"]*\)".*/\1/p' | head -n 1)

if [ -z "$CSRF_TOKEN" ]; then
  log "⚠️  Nie udało się pobrać tokena CSRF! (może sesja, brak HTTPS lub inny problem)"
else
  log "🔑 CSRF token: $CSRF_TOKEN"
fi

# 2️⃣ Wyślij formularz POST z przykładową stroną
curl -s -b "$TMP_COOKIE" -X POST "$BASE_URL/admin/?section=pages" \
  -d "csrf=$CSRF_TOKEN" \
  -d "action=save_page" \
  -d "title=Testowa strona (API)" \
  -d "slug=testowa-strona-api" \
  -d "body_html=<p>Treść testowa z API POST</p>" \
  -o /dev/null

if [ $? -eq 0 ]; then
  log "✅ Formularz POST wysłany pomyślnie."
else
  log "❌ Błąd podczas wysyłania POST!"
fi

log "=== ✅ END API POST TEST ==="
