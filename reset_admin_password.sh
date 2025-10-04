#!/usr/bin/env bash
set -euo pipefail

SCRIPT_NAME=$(basename "$0")
ENV_FILE=".env"
BACKUP_FILE=".env.bak"

usage() {
  cat <<USAGE
Użycie: $SCRIPT_NAME [opcje] [NOWE_HASŁO]

Opcje:
  --random       wygeneruj silne losowe hasło (16+ znaków)
  --show-hash    wylicz i pokaż hash bez modyfikacji plików
  --dry-run      pokaż zmiany w .env bez zapisywania
  -h, --help     pokaż tę pomoc

Przykłady:
  bash $SCRIPT_NAME "NoweHaslo123!"
  bash $SCRIPT_NAME --random
  bash $SCRIPT_NAME --show-hash "NoweHaslo123!"
USAGE
}

random=false
show_hash=false
dry_run=false
password_arg=""

while (($#)); do
  case "$1" in
    --random)
      random=true
      ;;
    --show-hash)
      show_hash=true
      ;;
    --dry-run)
      dry_run=true
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    --)
      shift
      if (($#)); then
        if [[ -n "$password_arg" ]]; then
          echo "❌ Podano zbyt wiele haseł." >&2
          exit 1
        fi
        password_arg="$1"
        shift
      fi
      break
      ;;
    -* )
      echo "❌ Nieznana opcja: $1" >&2
      usage >&2
      exit 1
      ;;
    *)
      if [[ -n "$password_arg" ]]; then
        echo "❌ Podano zbyt wiele haseł." >&2
        exit 1
      fi
      password_arg="$1"
      ;;
  esac
  shift || true
  if [[ $# -eq 0 ]]; then
    break
  fi
done

if $random && [[ -n "$password_arg" ]]; then
  echo "❌ Nie można jednocześnie podać hasła i użyć --random." >&2
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  echo "❌ Nie znaleziono polecenia 'php'. Zainstaluj php-cli (np. sudo apt install php-cli)." >&2
  exit 2
fi

if $random; then
  password=$(php -r 'echo rtrim(strtr(base64_encode(random_bytes(18)),"+/","-_"),"=");')
else
  password="$password_arg"
fi

if [[ -z "$password" ]]; then
  IFS= read -rs -p "🔑 Podaj nowe hasło administratora: " password
  echo
fi

if [[ -z "$password" ]]; then
  echo "❌ Hasło nie może być puste." >&2
  exit 1
fi

hash=$(php -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "$password")

if $show_hash; then
  echo "🔐 Hasło: $password"
  echo "AUTH_PASSWORD_HASH=\"$hash\""
  exit 0
fi

orig_tmp=$(mktemp)
if [[ -f "$ENV_FILE" ]]; then
  cp "$ENV_FILE" "$orig_tmp"
else
  : >"$orig_tmp"
fi

new_tmp=$(mktemp)
trap 'rm -f "$orig_tmp" "$new_tmp"' EXIT
cp "$orig_tmp" "$new_tmp"

if grep -q '^AUTH_PASSWORD_HASH=' "$new_tmp"; then
  sed -i "s|^AUTH_PASSWORD_HASH=.*|AUTH_PASSWORD_HASH=\"$hash\"|" "$new_tmp"
else
  if [[ -s "$new_tmp" ]]; then
    last_char=$(tail -c1 "$new_tmp" 2>/dev/null || true)
    if [[ "$last_char" != $'\n' ]]; then
      echo >>"$new_tmp"
    fi
  fi
  printf 'AUTH_PASSWORD_HASH="%s"\n' "$hash" >>"$new_tmp"
fi

if $dry_run; then
  if [[ ! -f "$ENV_FILE" ]]; then
    echo "ℹ️ Plik .env nie istnieje - zostałby utworzony."
  fi
  set +e
  diff -u "$orig_tmp" "$new_tmp"
  diff_status=$?
  set -e
  if [[ $diff_status -eq 0 ]]; then
    echo "ℹ️ Brak zmian do zastosowania."
  elif [[ $diff_status -gt 1 ]]; then
    echo "❌ Nie udało się porównać plików." >&2
    exit $diff_status
  fi
  echo "ℹ️ Tryb --dry-run: nie zapisano żadnych zmian."
  exit 0
fi

if [[ ! -f "$ENV_FILE" ]]; then
  : >"$ENV_FILE"
fi

cp "$ENV_FILE" "$BACKUP_FILE"
mv "$new_tmp" "$ENV_FILE"
chmod 600 "$ENV_FILE" 2>/dev/null || true

echo "✅ Zmieniono hasło admina"
echo "🔐 Nowe hasło: $password"
echo "⚠️ Zaloguj się i zmień to hasło jak najszybciej z poziomu panelu."
