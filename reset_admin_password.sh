#!/bin/bash

ENV_FILE="./.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "❌ Nie znaleziono pliku .env w bieżącym katalogu!"
  exit 1
fi

read -s -p "🔐 Podaj nowe hasło administratora: " PASSWORD
echo
read -s -p "🔁 Powtórz hasło: " PASSWORD_REPEAT
echo

if [[ "$PASSWORD" != "$PASSWORD_REPEAT" ]]; then
  echo "❌ Hasła nie są takie same!"
  exit 1
fi

if [[ ${#PASSWORD} -lt 8 ]]; then
  echo "❌ Hasło musi mieć co najmniej 8 znaków."
  exit 1
fi

# Wygeneruj hash w PHP
HASH=$(php -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);")

# Podmień linię w .env
if grep -q "AUTH_PASSWORD_HASH=" "$ENV_FILE"; then
  sed -i "s|^AUTH_PASSWORD_HASH=.*|AUTH_PASSWORD_HASH=\"$HASH\"|" "$ENV_FILE"
else
  echo "AUTH_PASSWORD_HASH=\"$HASH\"" >> "$ENV_FILE"
fi

echo "✅ Hasło zostało zresetowane."
echo "📌 Odśwież zmienne środowiskowe: source ~/.bashrc (lub użyj direnv/systemd)"
