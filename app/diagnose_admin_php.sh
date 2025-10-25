#!/bin/bash
echo "🔍 Diagnostyka plików PHP w public/admin/"

find public/admin -name "*.php" | while read -r file; do
  echo -n "🧪 $file ... "
  OUTPUT=$(php -l "$file" 2>&1)
  if [[ "$OUTPUT" == *"No syntax errors detected"* ]]; then
    echo "✅ OK"
  else
    echo "❌ Błąd"
    echo "$OUTPUT"
  fi
done
