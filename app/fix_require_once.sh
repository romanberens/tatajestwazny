#!/bin/bash
echo "🛠️  Naprawiam błędne require_once w admin/menu i admin/pages..."

TARGETS=(
  "public/admin/menu/index.php"
  "public/admin/pages/index.php"
)

for file in "${TARGETS[@]}"; do
  echo "🔧 Przetwarzam: $file"
  sed -i 's/__DIR__"\//__DIR__ . "\//g' "$file"
done

echo "✅ Gotowe. Teraz możesz ponownie uruchomić test:"
echo "./diagnose_admin_php.sh"
