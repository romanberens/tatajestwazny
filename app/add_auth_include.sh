#!/bin/bash
# Skrypt: Dodaje require_once '../auth.php'; na górze plików admin/blog

TARGET_FILES=(
  "public/admin/blog/index.php"
  "public/admin/blog/add.php"
  "public/admin/blog/edit.php"
  "public/admin/blog/delete.php"
)

for file in "${TARGET_FILES[@]}"; do
  if [[ -f "$file" ]]; then
    if grep -q "require_once '../auth.php';" "$file"; then
      echo "✅ $file - już zawiera auth.php"
    else
      # Dodajemy require_once na górze pliku (po <?php jeśli występuje)
      if grep -q "<?php" "$file"; then
        sed -i "0,/<?php/ s/<?php/<?php\nrequire_once '..\/auth.php';/" "$file"
        echo "✅ $file - dodano require_once"
      else
        echo "⚠️ $file - brak <?php, nie zmodyfikowano"
      fi
    fi
  else
    echo "❌ $file - plik nie istnieje"
  fi
done
