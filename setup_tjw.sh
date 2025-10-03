#!/bin/bash
set -e

BASE_DIR="$HOME/projects/tatajestwazny/public"
INCLUDES_DIR="$BASE_DIR/includes"
ICONS_DIR="$BASE_DIR/icons"

mkdir -p "$INCLUDES_DIR" "$ICONS_DIR"

# index.php
cat > "$BASE_DIR/index.php" <<'PHP'
<?php include('includes/header.php'); ?>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
  <?php include('sections/home.php'); ?>
  <?php include('sections/manifest.php'); ?>
  <?php include('sections/blog.php'); ?>
  <?php include('sections/kontakt.php'); ?>
</main>
<?php include('includes/footer.php'); ?>
PHP

# header.php
cat > "$INCLUDES_DIR/header.php" <<'PHP'
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tata Jest Ważny • Inicjatywa Marzeny Ciupek-Tarnawskiej</title>
  <meta name="description" content="Społeczna inicjatywa wsparcia rodziców dotkniętych alienacją rodzicielską. Rozmowa, wiedza, kontakt. Pro bono.">
  <meta name="theme-color" content="#f97316">
  <meta name="color-scheme" content="light">
  <link rel="manifest" href="/manifest.webmanifest">
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            ui: ['Inter', 'system-ui', 'sans-serif']
          },
          colors: {
            brand: { 50: '#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',400:'#fb923c',500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412',900:'#7c2d12' },
            calm: { 50:'#fdf2f8',100:'#fce7f3',200:'#fbcfe8',300:'#f9a8d4',400:'#f472b6' }
          },
          boxShadow: { soft: '0 10px 30px -12px rgba(16,24,40,.1)' }
        }
      }
    }
  </script>
  <style>
    .fade-enter{opacity:0}.fade-enter-active{opacity:1;transition:opacity .35s ease}
    .fade-leave{opacity:1}.fade-leave-active{opacity:0;transition:opacity .25s ease}
    @media (prefers-reduced-motion: reduce){
      .fade-enter-active,.fade-leave-active{transition:none!important}
    }
    :focus-visible{outline: 3px solid #fb923c; outline-offset: 2px;}
  </style>
</head>
<body class="bg-brand-50 text-slate-800 antialiased font-ui">
PHP

# footer.php
cat > "$INCLUDES_DIR/footer.php" <<'PHP'
  <footer class="border-t border-orange-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm text-slate-600 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <p>© <?php echo date('Y'); ?> tatajestwazny.pl — Inicjatywa Marzeny Ciupek-Tarnawskiej</p>
      <p>Hosting i opieka technologiczna: <span class="font-medium">OneNetworks</span></p>
    </div>
  </footer>
  <script src="/main.js"></script>
</body>
</html>
PHP

# offline.php
cat > "$BASE_DIR/offline.php" <<'PHP'
<?php include('includes/header.php'); ?>
<main class="max-w-xl mx-auto pt-24 pb-12 text-center">
  <h1 class="text-2xl font-semibold">Jesteś offline 📴</h1>
  <p class="mt-3 text-slate-600">Brak połączenia z Internetem.<br>Spróbuj ponownie później.</p>
  <a href="/" class="mt-5 inline-block px-4 py-2 rounded-xl bg-brand-500 text-white hover:bg-brand-600">Wróć do strony głównej</a>
</main>
<?php include('includes/footer.php'); ?>
PHP

# manifest.webmanifest
cat > "$BASE_DIR/manifest.webmanifest" <<'JSON'
{
  "name": "Tata Jest Ważny",
  "short_name": "TJW",
  "start_url": "/index.php",
  "display": "standalone",
  "background_color": "#fff7ed",
  "theme_color": "#f97316",
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
JSON

# sw.js
cat > "$BASE_DIR/sw.js" <<'JS'
const CACHE = 'tjw-v1';
const ASSETS = [
  '/',
  '/index.php',
  '/offline.php',
  '/styles.css',
  '/manifest.webmanifest',
  '/icons/icon-192.png',
  '/icons/icon-512.png'
];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(ASSETS)));
  self.skipWaiting();
});

self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys =>
    Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
  ));
  self.clients.claim();
});

self.addEventListener('fetch', e => {
  const req = e.request;
  const isHTML = req.headers.get('accept')?.includes('text/html');

  if (isHTML) {
    e.respondWith(
      fetch(req).then(res => {
        const copy = res.clone();
        caches.open(CACHE).then(c => c.put(req, copy));
        return res;
      }).catch(() => caches.match('/offline.php'))
    );
  } else {
    e.respondWith(
      caches.match(req).then(res => res || fetch(req).then(r => {
        const copy = r.clone();
        caches.open(CACHE).then(c => c.put(req, copy));
        return r;
      }))
    );
  }
});
JS

# main.js
cat > "$BASE_DIR/main.js" <<'JS'
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('[SW] Zarejestrowany:', reg.scope))
      .catch(err => console.error('[SW] Błąd:', err));
  });
}
JS

echo "✅ Pliki aplikacji TataJestWazny zostały utworzone/nadpisane w $BASE_DIR"
