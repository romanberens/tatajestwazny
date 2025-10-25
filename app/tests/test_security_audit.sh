#!/bin/bash
# 🧩 test_security_audit.sh
# Audyt podstawowych zabezpieczeń CMS "Tata Jest Ważny"
# Wykrywa brak CSRF, brak sanitizacji HTML i bezpośredni dostęp do $_POST

BASE="/var/www/html/app"
LOGDIR="$BASE/logs/diagnostics"
mkdir -p "$LOGDIR"
LOGFILE="$LOGDIR/cms_security_audit_$(date +%Y%m%d_%H%M%S).log"

log() {
  echo "[$(date '+%F %T')] $1" | tee -a "$LOGFILE"
}

log "=== 🔍 START SECURITY AUDIT ==="

# -------------------------------
# 1️⃣ Sprawdź obecność require_post_csrf() w funkcjach handle*
# -------------------------------
for handler in $(grep -l "function handle" "$BASE/src/"*.php); do
  name=$(basename "$handler")
  grep -q "require_post_csrf" "$handler"
  if [[ $? -eq 0 ]]; then
    log "[SECURITY] $name ✅ zawiera require_post_csrf()"
  else
    log "[SECURITY] $name ⚠️ brak wywołania require_post_csrf()"
  fi
done

# -------------------------------
# 2️⃣ Sprawdź obecność safe_html() lub htmlspecialchars w helperach
# -------------------------------
grep -q "safe_html" "$BASE/src/helpers.php"
if [[ $? -eq 0 ]]; then
  log "[SECURITY] helpers.php ✅ zawiera funkcję safe_html()"
else
  log "[SECURITY] helpers.php ⚠️ brak safe_html() (możliwy XSS)"
fi

grep -q "htmlspecialchars" "$BASE/src/helpers.php"
if [[ $? -eq 0 ]]; then
  log "[SECURITY] helpers.php ✅ używa htmlspecialchars()"
else
  log "[SECURITY] helpers.php ⚠️ brak htmlspecialchars() (brak escapingu w renderze)"
fi

# -------------------------------
# 3️⃣ Sprawdź walidację wejścia w repozytoriach (empty(), trim())
# -------------------------------
for repo in "$BASE/src/Repositories/"*.php; do
  name=$(basename "$repo")
  if grep -q "trim(" "$repo"; then
    log "[SECURITY] $name ✅ używa trim() do czyszczenia danych"
  else
    log "[SECURITY] $name ⚠️ brak trim()"
  fi

  if grep -q "empty(" "$repo"; then
    log "[SECURITY] $name ✅ sprawdza empty() (walidacja wymaganych pól)"
  else
    log "[SECURITY] $name ⚠️ brak empty() w walidacji"
  fi
done

# -------------------------------
# 4️⃣ Sprawdź obecność pól CSRF w formularzach (templates/admin_*)
# -------------------------------
for form in "$BASE/templates/admin_"*.php; do
  name=$(basename "$form")
  if grep -q 'name="csrf"' "$form"; then
    log "[SECURITY] $name ✅ ma pole hidden csrf"
  else
    log "[SECURITY] $name ⚠️ brak pola csrf"
  fi
done

# -------------------------------
# 5️⃣ Sprawdź bezpośrednie użycia $_POST bez filtracji
# -------------------------------
for file in $(grep -rl "\$_POST" "$BASE/src" "$BASE/public/admin" 2>/dev/null); do
  name=$(basename "$file")
  if grep -q "filter_input" "$file"; then
    log "[SECURITY] $name ✅ używa filter_input()"
  else
    log "[SECURITY] $name ⚠️ bezpośredni dostęp do \$_POST (brak filtracji)"
  fi
done

log "=== ✅ END SECURITY AUDIT ==="
echo "📜 Raport zapisano do: $LOGFILE"
