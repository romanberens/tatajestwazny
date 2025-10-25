#!/bin/bash

set -e

PROJECT_DIR=~/projects/tatajestwazny
PUBLIC_DIR="$PROJECT_DIR/public"
INCLUDES_DIR="$PROJECT_DIR/includes"
CONFIG_DIR="$PROJECT_DIR/config"

echo "📁 Tworzenie struktury katalogów..."

mkdir -p "$PUBLIC_DIR/icons"
mkdir -p "$PUBLIC_DIR/js"
mkdir -p "$INCLUDES_DIR"
mkdir -p "$CONFIG_DIR"

echo "✅ Katalogi utworzone."

echo "📝 Tworzenie plików i wstawianie kodu..."

# index.php
cat > "$PUBLIC_DIR/index.php" << 'EOF'
<?php include('../includes/header.php'); ?>
<main>
  <h1>Witaj w Tata Jest Ważny ✅</h1>
  <p>Aplikacja działa! PWA i PHP są gotowe.</p>
</main>
<?php include('../includes/footer.php'); ?>
EOF

# header.php
cat > "$INCLUDES_DIR/header.php" << 'EOF'
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tata Jest Ważny</title>
  <link rel="stylesheet" href="/styles.css">
  <link rel="manifest" href="/manifest.webmanifest">
  <meta name="theme-color" content="#f2611c">
</head>
<body>
<header>
  <h2>Tata Jest Ważny</h2>
</header>
EOF

# footer.php
cat > "$INCLUDES_DIR/footer.php" << 'EOF'
<footer>
  <p style="font-size: 0.9em; color: gray;">&copy; 2025 Tata Jest Ważny</p>
</footer>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then(() => {
      console.log('✅ Service Worker zarejestrowany');
    });
  }
</script>
</body>
</html>
EOF

# styles.css
cat > "$PUBLIC_DIR/styles.css" << 'EOF'
body {
  font-family: 'Segoe UI', sans-serif;
  margin: 0 auto;
  max-width: 800px;
  padding: 2rem;
  background: #fafafa;
  color: #222;
}
h1 {
  color: #f2611c;
}
footer {
  margin-top: 2rem;
  border-top: 1px solid #ddd;
  padding-top: 1rem;
}
EOF

# manifest.webmanifest
cat > "$PUBLIC_DIR/manifest.webmanifest" << 'EOF'
{
  "name": "Tata Jest Ważny",
  "short_name": "TJW",
  "start_url": "/index.php",
  "display": "standalone",
  "background_color": "#fafafa",
  "theme_color": "#f2611c",
  "icons": [
    {
      "src": "/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
EOF

# sw.js
cat > "$PUBLIC_DIR/sw.js" << 'EOF'
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open("tjw-cache").then((cache) => {
      return cache.addAll([
        "/",
        "/index.php",
        "/styles.css",
        "/manifest.webmanifest"
      ]);
    })
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
EOF

# config.php (pusty na start)
touch "$CONFIG_DIR/config.php"

echo "🎉 Gotowe! Struktura aplikacji PHP + PWA została utworzona w:"
echo "➡️ $PROJECT_DIR"
