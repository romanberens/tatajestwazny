#!/bin/sh
# 🛡️ test_security_extended.sh
# Audyt rozszerzony: XSS, SQLi, hasła, brak bindParam, echo $_POST, itp. (BusyBox-friendly)

BASE="/var/www/html/app"
LOGDIR="$BASE/logs/diagnostics"
mkdir -p "$LOGDIR"
LOGFILE="$LOGDIR/cms_security_extended_$(date +%Y%m%d_%H%M%S).log"
MAX_HITS=50   # maksymalna liczba wyników na sekcję

log() {
  echo "[$(date '+%F %T')] $1" | tee -a "$LOGFILE"
}

sep() {
  echo "------------------------------------------------------------" | tee -a "$LOGFILE"
}

log "=== 🔍 START EXTENDED SECURITY AUDIT ==="

# Funkcja pomocnicza
search() {
  desc="$1"
  pattern="$2"
  log "[CHECK] $desc"
  sep
  find "$BASE" -type f -name '*.php' \
    ! -path "$BASE/logs/*" \
    ! -path "$BASE/.git/*" \
    ! -path "$BASE/vendor/*" \
    ! -path "$BASE/node_modules/*" \
    -print0 \
  | xargs -0 grep -n -E "$pattern" 2>/dev/null | head -n "$MAX_HITS" | tee -a "$LOGFILE"
  sep
}

# 1️⃣ XSS — echo $_POST / $_GET / $_REQUEST
search "XSS: echo \$_POST / \$_GET / \$_REQUEST" "echo[[:space:]]+\\\$_(POST|GET|REQUEST)"

# 2️⃣ Echo zmiennych bez htmlspecialchars
search "Echo zmiennych bez htmlspecialchars" "echo[[:space:]]+[^;]*\\\$[A-Za-z_]"

# 3️⃣ SQLi — prepare z interpolacją zmiennych
search "SQLi: prepare(...) z interpolacją zmiennych" "prepare\\([^)]*\\$"

# 4️⃣ execute() — sprawdź brak '?'
log "[CHECK] PDO execute() — brak placeholderów '?'"
sep
find "$BASE/src/Repositories" -type f -name '*.php' -print0 \
  | xargs -0 grep -n "->execute(" 2>/dev/null | grep -v "?" | head -n "$MAX_HITS" | tee -a "$LOGFILE"
sep

# 5️⃣ Hardcoded passwords (nie password_hash)
log "[CHECK] Hardcoded 'password' (bez password_hash)"
sep
find "$BASE" -type f -name '*.php' ! -path "$BASE/logs/*" -print0 \
  | xargs -0 grep -n -E "password(?!_hash)" 2>/dev/null | head -n "$MAX_HITS" | tee -a "$LOGFILE"
sep

# 6️⃣ AUTH_PASSWORD w kodzie
search "AUTH_PASSWORD / AUTH_PASSWORD_HASH" "AUTH_PASSWORD"

# 7️⃣ Echo $_SERVER — możliwy leak
search "Echo \$_SERVER — możliwe ujawnienie danych" "echo[[:space:]]+\\\$_SERVER"

log "=== ✅ END EXTENDED SECURITY AUDIT ==="
log "📜 Raport zapisano do: $LOGFILE"
