#!/bin/bash

echo "🔍 TEST AUTORYZACJI I ŚRODOWISKA (.env + password_verify + curl)"

echo -e "\n📦 1. Sprawdzanie pliku .env..."
if [[ ! -f .env ]]; then
  echo "❌ Brak pliku .env"
  exit 1
else
  grep AUTH_PASSWORD_HASH .env || echo "❌ Brak AUTH_PASSWORD_HASH w .env"
fi

echo -e "\n🔐 2. Testowanie password_verify w PHP..."
HASH=$(grep AUTH_PASSWORD_HASH .env | cut -d'=' -f2 | tr -d '"')
PHP_TEST=$(php -r "echo password_verify('Entropia.2025', '$HASH') ? '✅ OK' : '❌ BŁĄD';")
echo "Wynik password_verify(): $PHP_TEST"

echo -e "\n📄 3. Sprawdzanie kodu auth.php..."
if grep -q "password_verify" public/auth.php; then
  echo "✅ password_verify obecne w auth.php"
else
  echo "❌ Brak password_verify w auth.php"
fi

echo -e "\n🌐 4. Test logowania przez curl..."
curl -s -i -X POST http://localhost/admin/blog/index.php \
  -c test_cookies.txt \
  -d "password=Entropia.2025" | grep -q "Nieprawidłowe hasło" \
  && echo "❌ Logowanie nieudane (Nieprawidłowe hasło!)" \
  || echo "✅ Logowanie udane lub brak błędu"

echo -e "\n🍪 5. Sprawdzenie sesji przez ciasteczko..."
curl -s -i http://localhost/admin/blog/index.php -b test_cookies.txt \
  | grep -q "Logowanie" \
  && echo "❌ Nadal pokazuje formularz logowania" \
  || echo "✅ Zalogowano (formularz ukryty)"

echo -e "\n✅ TESTY ZAKOŃCZONE"
