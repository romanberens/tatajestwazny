#!/bin/bash

echo "🔧 Naprawianie ścieżek require() w katalogu public/admin"

find public/admin -type f -name "*.php" | while read file; do
    sed -i -E \
        -e 's@require_once\s+["'"'"']\.\./\.\./includes/(.+?)["'"'"']@require_once __DIR__ . "/../../includes/\1"@g' \
        -e 's@require_once\s+["'"'"']\.\./includes/(.+?)["'"'"']@require_once __DIR__ . "/../includes/\1"@g' \
        -e 's@require_once\s+["'"'"']\.\./auth.php["'"'"']@require_once __DIR__ . "/../includes/auth.php"@g' \
        -e 's@require\s+["'"'"']\.\./\.\./includes/(.+?)["'"'"']@require __DIR__ . "/../../includes/\1"@g' \
        -e 's@require\s+["'"'"']\.\./includes/(.+?)["'"'"']@require __DIR__ . "/../includes/\1"@g' \
        "$file"
    echo "✅ Poprawiono: $file"
done

echo "🎉 Wszystkie ścieżki require() zostały zaktualizowane."
