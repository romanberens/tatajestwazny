#!/bin/bash

echo "🔍 DEBUG HASHOWANIA HASŁA (reset_admin_password.sh, .env, PHP)"

# 1. Wyciągnij hash z pliku .env
HASH_LINE=$(grep AUTH_PASSWORD_HASH .env)
HASH=$(echo "$HASH_LINE" | cut -d'=' -f2- | tr -d '"')

echo -e "\n🔐 1. Wczytany hash z .env:"
echo "$HASH"

# 2. Czy wygląda jak poprawny bcrypt?
if [[ $HASH =~ ^\$2[aby]?\$[0-9]{2}\$.*$ ]]; then
  echo "✅ Format hash wygląda poprawnie (bcrypt)"
else
  echo "❌ Format nie wygląda jak poprawny hash bcrypt!"
fi

# 3. Test manualnego hashowania
TEST_PASS="Entropia.2025"
PHP_VERIFY=$(php -r "echo password_verify('$TEST_PASS', '$HASH') ? '✅ Hasło OK' : '❌ Hasło NIE PASUJE';")
echo -e "\n🧪 2. Test password_verify() w PHP:\n$PHP_VERIFY"

# 4. Dodatkowo spróbuj hashować i porównać
NEW_HASH=$(php -r "echo password_hash('$TEST_PASS', PASSWORD_BCRYPT);")
echo -e "\n🔁 3. Nowy hash tego samego hasła (dla porównania):"
echo "$NEW_HASH"

# 5. Sprawdzenie czy obecny kod ładuje hasło poprawnie
echo -e "\n🌍 4. Sprawdzenie dostępności AUTH_PASSWORD_HASH przez PHP (getenv + $_ENV):"
php -r "
\$env = getenv('AUTH_PASSWORD_HASH');
\$superglobal = \$_ENV['AUTH_PASSWORD_HASH'] ?? null;
echo 'getenv(): '.(\$env ? '[OK]' : '[BRAK]') . PHP_EOL;
echo '\$_ENV[]: '.(\$superglobal ? '[OK]' : '[BRAK]') . PHP_EOL;
"

# 6. Szybka walidacja auth.php jeśli istnieje
if [[ -f public/auth.php ]]; then
  echo -e "\n📄 5. Sprawdzenie, czy auth.php ładuje .env..."
  if grep -q "AUTH_PASSWORD_HASH" public/auth.php; then
    echo "✅ auth.php używa AUTH_PASSWORD_HASH"
  else
    echo "❌ auth.php nie używa AUTH_PASSWORD_HASH!"
  fi
fi

echo -e "\n✅ DEBUG ZAKOŃCZONY"
echo "🔧 Jeśli chcesz naprawić, uruchom ponownie: ./reset_admin_password.sh"
