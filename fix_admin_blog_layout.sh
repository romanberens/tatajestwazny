#!/bin/bash
echo "🔧 Fixing admin/blog layout integration..."

BLOG_DIR="public/admin/blog"
LAYOUT_INCLUDE='require_once "../../includes/layout.php";'
AUTH_INCLUDE='require_once "../../includes/auth.php"; auth();'

for file in "$BLOG_DIR"/*.php; do
  echo "🛠️ Processing $file"

  # Backup
  cp "$file" "$file.bak"

  # Remove full HTML headers/footers
  sed -i '/<!DOCTYPE html>/d' "$file"
  sed -i '/<html.*>/d' "$file"
  sed -i '/<\/html>/d' "$file"
  sed -i '/<head>/d' "$file"
  sed -i '/<\/head>/d' "$file"
  sed -i '/<body>/d' "$file"
  sed -i '/<\/body>/d' "$file"
  sed -i '/<title>.*<\/title>/d' "$file"
  sed -i '/<meta .*charset=/d' "$file"

  # Remove redundant require_once '../auth.php';
  sed -i '/require_once .*auth.php/d' "$file"

  # Check if layout is already included
  grep -q "$LAYOUT_INCLUDE" "$file" || sed -i "1i<?php $LAYOUT_INCLUDE ?>" "$file"

  # Check if auth() is already present
  grep -q "auth();" "$file" || sed -i "2i<?php $AUTH_INCLUDE ?>" "$file"

  echo "✅ Cleaned $file"
done

echo "🎉 Done. Backup files with .bak extension were created."
