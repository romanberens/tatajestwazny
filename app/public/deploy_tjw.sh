#!/usr/bin/env bash
set -euo pipefail

ROOT="$HOME/projects/tatajestwazny"
PUB="$ROOT/public"
SRC="$ROOT/src"
TPL="$ROOT/templates"
DBDIR="$ROOT/db"
STO="$ROOT/storage/cache"
ADM="$PUB/admin"
ICN="$PUB/icons"

echo "🚀 Deploy TJW (PHP + SQLite + PWA) do: $ROOT"

mkdir -p "$PUB" "$SRC" "$TPL" "$DBDIR" "$STO" "$ADM" "$ICN"

############################################
# 1) TEMPLATES (layouty + widoki)
############################################
cat > "$TPL/layout.php" <<'PHP'
<?php /** @var string $title */ /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Tata Jest Ważny') ?></title>
  <meta name="description" content="Społeczna inicjatywa wsparcia rodziców dotkniętych alienacją rodzicielską. Rozmowa, wiedza, kontakt. Pro bono.">
  <meta name="theme-color" content="#f97316">
  <meta name="color-scheme" content="light">
  <link rel="manifest" href="/manifest.webmanifest">
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <style>
    :focus-visible{outline:3px solid #fb923c;outline-offset:2px}
    .shadow-soft{box-shadow:0 10px 30px -12px rgba(16,24,40,.1)}
  </style>
</head>
<body class="bg-[#fff7ed] text-slate-800 antialiased">
  <?= $content ?>
  <footer class="border-t border-orange-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm text-slate-600 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <p>© <?= date('Y') ?> tatajestwazny.pl — Inicjatywa Marzeny Ciupek-Tarnawskiej</p>
      <p>Hosting i opieka technologiczna: <span class="font-medium">OneNetworks</span></p>
    </div>
  </footer>
  <script src="/main.js"></script>
</body>
</html>
PHP

cat > "$TPL/home.php" <<'PHP'
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
  <!-- Misja -->
  <section class="bg-white rounded-2xl p-6 shadow-soft mb-6">
    <h2 class="text-2xl font-semibold mb-3"><?= htmlspecialchars($misja[0]['title'] ?? 'Misja') ?></h2>
    <div class="prose prose-orange max-w-none text-slate-800">
      <?= $misja[0]['body_html'] ?? '<p>Uzupełnij treść w panelu.</p>' ?>
    </div>
  </section>

  <!-- Jak pomagam -->
  <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <article class="bg-white rounded-2xl p-6 shadow-soft lg:col-span-2">
      <h2 class="text-2xl font-semibold mb-3">Jak pomagam</h2>
      <?php foreach ($jak_pomagam as $b): ?>
        <div class="mb-5">
          <?php if (!empty($b['title'])): ?><h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($b['title']) ?></h3><?php endif; ?>
          <div class="prose max-w-none"><?= $b['body_html'] ?></div>
        </div>
      <?php endforeach; ?>
    </article>

    <!-- Szybka pomoc -->
    <aside class="bg-white rounded-2xl p-6 shadow-soft">
      <h3 class="text-lg font-semibold mb-3"><?= htmlspecialchars($szybka_pomoc[0]['title'] ?? 'Szybka pomoc') ?></h3>
      <div class="prose max-w-none"><?= $szybka_pomoc[0]['body_html'] ?? '' ?></div>
    </aside>
  </section>

  <!-- Dla kogo -->
  <section class="bg-white rounded-2xl p-6 shadow-soft">
    <h2 class="text-2xl font-semibold mb-3"><?= htmlspecialchars($dla_kogo[0]['title'] ?? 'Dla kogo') ?></h2>
    <div class="prose max-w-none"><?= $dla_kogo[0]['body_html'] ?? '' ?></div>
  </section>
</main>
PHP

cat > "$TPL/admin_layout.php" <<'PHP'
<?php /** @var string $title */ /** @var string $content */ ?>
<!doctype html><html lang="pl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin • <?= htmlspecialchars($title??'') ?></title>
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head><body class="bg-slate-50 text-slate-800">
<header class="bg-white border-b">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
    <h1 class="font-semibold">Panel administracyjny</h1>
    <nav class="text-sm flex gap-4">
      <a href="/admin/?page=blocks" class="hover:underline">Bloki</a>
      <a href="/admin/?action=logout" class="text-red-600 hover:underline">Wyloguj</a>
    </nav>
  </div>
</header>
<main class="max-w-5xl mx-auto px-4 py-6">
  <?= $content ?>
</main>
</body></html>
PHP

cat > "$TPL/admin_blocks_list.php" <<'PHP'
<h2 class="text-xl font-semibold mb-4">Bloki treści</h2>
<div class="mb-4">
  <a class="inline-block px-3 py-2 rounded bg-emerald-600 text-white" href="/admin/?action=edit">+ Nowy blok</a>
</div>
<table class="w-full text-sm bg-white rounded shadow-soft overflow-hidden">
  <thead class="bg-slate-100 text-left">
    <tr><th class="p-2">ID</th><th class="p-2">Region</th><th class="p-2">Tytuł</th><th class="p-2">Pozycja</th><th class="p-2">Widoczny</th><th class="p-2">Akcje</th></tr>
  </thead>
  <tbody>
  <?php foreach ($items as $it): ?>
    <tr class="border-t">
      <td class="p-2"><?= (int)$it['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($it['region']) ?></td>
      <td class="p-2"><?= htmlspecialchars($it['title'] ?? '') ?></td>
      <td class="p-2"><?= (int)$it['position'] ?></td>
      <td class="p-2"><?= $it['visible'] ? 'tak' : 'nie' ?></td>
      <td class="p-2 flex gap-2">
        <a class="px-2 py-1 rounded border" href="/admin/?action=move&id=<?= (int)$it['id'] ?>&d=-1">↑</a>
        <a class="px-2 py-1 rounded border" href="/admin/?action=move&id=<?= (int)$it['id'] ?>&d=1">↓</a>
        <a class="px-2 py-1 rounded border" href="/admin/?action=edit&id=<?= (int)$it['id'] ?>">Edytuj</a>
        <a class="px-2 py-1 rounded border text-red-600" href="/admin/?action=delete&id=<?= (int)$it['id'] ?>&csrf=<?= htmlspecialchars($_SESSION['csrf']) ?>" onclick="return confirm('Usunąć?')">Usuń</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
PHP

cat > "$TPL/admin_block_form.php" <<'PHP'
<h2 class="text-xl font-semibold mb-4"><?= !empty($block['id']) ? 'Edycja bloku' : 'Nowy blok' ?></h2>
<form method="post" class="bg-white p-4 rounded shadow-soft space-y-3">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <label class="block">Region
    <select name="region" class="border p-2 rounded w-full">
      <?php foreach ($regions as $r): ?>
        <option value="<?= $r ?>" <?= ($block['region'] ?? '')===$r ? 'selected':'' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="block">Tytuł
    <input type="text" name="title" value="<?= htmlspecialchars($block['title'] ?? '') ?>" class="border p-2 rounded w-full">
  </label>
  <label class="block">Treść (HTML)
    <textarea name="body_html" rows="10" class="border p-2 rounded w-full"><?= htmlspecialchars($block['body_html'] ?? '') ?></textarea>
  </label>
  <div class="flex gap-4 items-center">
    <label>Pozycja <input type="number" name="position" value="<?= (int)($block['position'] ?? 0) ?>" class="border p-2 rounded w-24"></label>
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="visible" value="1" <?= !empty($block['visible']) ? 'checked':'' ?>> Widoczny</label>
  </div>
  <div class="flex gap-2">
    <button class="px-4 py-2 rounded bg-brand-600 text-white" style="background:#ea580c">Zapisz</button>
    <a class="px-4 py-2 rounded border" href="/admin/?page=blocks">Anuluj</a>
  </div>
</form>
PHP

############################################
# 2) SRC (bootstrap, DB, DAO, helpers)
############################################
cat > "$SRC/bootstrap.php" <<'PHP'
<?php
session_start();
require __DIR__ . '/helpers.php';
require __DIR__ . '/Db.php';
require __DIR__ . '/Blocks.php';
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
PHP

cat > "$SRC/helpers.php" <<'PHP'
<?php
function render(string $template, array $vars = [], string $layout='layout.php'): void {
  extract($vars, EXTR_SKIP);
  ob_start(); include __DIR__ . '/../templates/' . $template; $content = ob_get_clean();
  $title = $vars['title'] ?? 'Tata Jest Ważny';
  include __DIR__ . '/../templates/' . $layout;
}
function render_admin(string $template, array $vars=[]): void {
  render($template, $vars, 'admin_layout.php');
}
function redirect(string $url): never { header("Location: $url"); exit; }
function require_post_csrf(): void {
  if ($_SERVER['REQUEST_METHOD']!=='POST' || empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    http_response_code(400); echo "Bad CSRF"; exit;
  }
}
PHP

cat > "$SRC/Db.php" <<'PHP'
<?php
final class Db {
  public PDO $pdo;
  public function __construct() {
    $this->pdo = new PDO('sqlite:' . __DIR__ . '/../db/tjw.sqlite', null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $this->pdo->exec("PRAGMA journal_mode=WAL;");
    $this->pdo->exec("PRAGMA foreign_keys=ON;");
    $this->pdo->exec("PRAGMA synchronous=NORMAL;");
  }
}
PHP

cat > "$SRC/Blocks.php" <<'PHP'
<?php
final class Blocks {
  public function __construct(private PDO $pdo) {}
  public function byRegion(string $region): array {
    $st=$this->pdo->prepare("SELECT * FROM content_blocks WHERE region=? AND visible=1 ORDER BY position ASC, id ASC");
    $st->execute([$region]); return $st->fetchAll();
  }
  public function all(): array {
    return $this->pdo->query("SELECT * FROM content_blocks ORDER BY region, position, id")->fetchAll();
  }
  public function find(int $id): ?array {
    $st=$this->pdo->prepare("SELECT * FROM content_blocks WHERE id=?");
    $st->execute([$id]); $r=$st->fetch(); return $r?:null;
  }
  public function save(array $d): int {
    $visible = !empty($d['visible']) ? 1 : 0;
    $pos = (int)($d['position'] ?? 0);
    if (!empty($d['id'])) {
      $st=$this->pdo->prepare("UPDATE content_blocks SET region=?,title=?,body_html=?,position=?,visible=?,updated_at=CURRENT_TIMESTAMP WHERE id=?");
      $st->execute([$d['region'],$d['title'],$d['body_html'],$pos,$visible,(int)$d['id']]);
      return (int)$d['id'];
    } else {
      $st=$this->pdo->prepare("INSERT INTO content_blocks(region,title,body_html,position,visible) VALUES(?,?,?,?,?)");
      $st->execute([$d['region'],$d['title'],$d['body_html'],$pos,$visible]);
      return (int)$this->pdo->lastInsertId();
    }
  }
  public function delete(int $id): void {
    $st=$this->pdo->prepare("DELETE FROM content_blocks WHERE id=?");
    $st->execute([$id]);
  }
  public function move(int $id, int $delta): void {
    $blk = $this->find($id); if(!$blk) return;
    $newPos = max(0, (int)$blk['position'] + $delta);
    $this->pdo->beginTransaction();
    $swap=$this->pdo->prepare("UPDATE content_blocks SET position=? WHERE region=? AND position=? AND id!=?");
    $swap->execute([(int)$blk['position'],$blk['region'],$newPos,$id]);
    $upd=$this->pdo->prepare("UPDATE content_blocks SET position=? WHERE id=?");
    $upd->execute([$newPos,$id]);
    $this->pdo->commit();
  }
}
PHP

############################################
# 3) FRONT PUBLIC (index + PWA)
############################################
cat > "$PUB/index.php" <<'PHP'
<?php
require __DIR__ . '/../src/bootstrap.php';
$db = new Db(); $blocks = new Blocks($db->pdo);

$misja        = $blocks->byRegion('home.misja');
$jak_pomagam  = $blocks->byRegion('home.jak_pomagam');
$szybka_pomoc = $blocks->byRegion('home.szybka_pomoc');
$dla_kogo     = $blocks->byRegion('home.dla_kogo');

render('home.php', compact('misja','jak_pomagam','szybka_pomoc','dla_kogo') + ['title'=>'Tata Jest Ważny']);
PHP

cat > "$PUB/offline.php" <<'PHP'
<?php require __DIR__ . '/../src/bootstrap.php'; ?>
<?php ob_start(); ?>
<main class="max-w-xl mx-auto pt-24 pb-12 text-center">
  <h1 class="text-2xl font-semibold">Jesteś offline 📴</h1>
  <p class="mt-3 text-slate-600">Brak połączenia z Internetem. Spróbuj ponownie później.</p>
  <a href="/" class="mt-5 inline-block px-4 py-2 rounded-xl text-white" style="background:#ea580c">Wróć do strony głównej</a>
</main>
<?php $content = ob_get_clean(); $title='Offline'; include __DIR__ . '/../templates/layout.php'; ?>
PHP

cat > "$PUB/main.js" <<'JS'
if ('serviceWorker' in navigator) {
  addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('[SW] scope:', reg.scope))
      .catch(err => console.error('[SW] error:', err));
  });
}
JS

cat > "$PUB/sw.js" <<'JS'
const CACHE = 'tjw-v2';
const ASSETS = [
  '/', '/index.php', '/offline.php',
  '/manifest.webmanifest',
  '/icons/icon-192.png', '/icons/icon-512.png'
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
        caches.open(CACHE).then(c => c.put(req, res.clone()));
        return res;
      }).catch(() => caches.match('/offline.php'))
    );
  } else {
    e.respondWith(
      caches.match(req).then(m => m || fetch(req).then(res => {
        caches.open(CACHE).then(c => c.put(req, res.clone())); return res;
      }))
    );
  }
});
JS

cat > "$PUB/manifest.webmanifest" <<'JSON'
{
  "name": "Tata Jest Ważny",
  "short_name": "TJW",
  "start_url": "/index.php",
  "display": "standalone",
  "background_color": "#fff7ed",
  "theme_color": "#f97316",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
JSON

# placeholdery ikon (proste kwadraty), jeśli brak
if [ ! -f "$ICN/icon-192.png" ]; then
  convert -size 192x192 canvas:"#f97316" -gravity center -fill white -pointsize 96 -font DejaVu-Sans -annotate 0 "TJ" "$ICN/icon-192.png" 2>/dev/null || true
fi
if [ ! -f "$ICN/icon-512.png" ]; then
  convert -size 512x512 canvas:"#f97316" -gravity center -fill white -pointsize 256 -font DejaVu-Sans -annotate 0 "TJ" "$ICN/icon-512.png" 2>/dev/null || true
fi

############################################
# 4) ADMIN (login + listing + edycja)
############################################
# proste dane logowania
cat > "$ADM/auth.php" <<'PHP'
<?php
const ADMIN_USER = 'admin';
const ADMIN_PASS_HASH = '$2y$10$8m2m5wz3rtt8bqgZqO7W5O5m8e8s3S1m2u9mUQ3qgEoZqjZc5w0dW'; // hasło: changeme
PHP

cat > "$ADM/index.php" <<'PHP'
<?php
require __DIR__ . '/../../src/bootstrap.php';
require __DIR__ . '/auth.php';

function ensure_logged() {
  if (empty($_SESSION['admin'])) { header('Location: /admin/?action=login'); exit; }
}
$db = new Db(); $blocks = new Blocks($db->pdo);
$regions = ['home.misja','home.jak_pomagam','home.szybka_pomoc','home.dla_kogo'];

$action = $_GET['action'] ?? ($_GET['page'] ?? 'blocks');

if ($action === 'login') {
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = $_POST['user'] ?? ''; $p = $_POST['pass'] ?? '';
    if ($u === ADMIN_USER && password_verify($p, ADMIN_PASS_HASH)) {
      $_SESSION['admin'] = true; redirect('/admin/?page=blocks');
    }
    $error = 'Błędne dane logowania';
  }
  ob_start(); ?>
  <div class="max-w-sm mx-auto bg-white p-5 rounded shadow-soft">
    <h1 class="text-lg font-semibold mb-3">Logowanie</h1>
    <?php if (!empty($error)): ?><p class="text-red-600 mb-2"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
      <label class="block mb-2">Użytkownik
        <input class="border p-2 rounded w-full" name="user" autocomplete="username">
      </label>
      <label class="block mb-3">Hasło
        <input type="password" class="border p-2 rounded w-full" name="pass" autocomplete="current-password">
      </label>
      <button class="px-4 py-2 rounded text-white" style="background:#ea580c">Zaloguj</button>
    </form>
  </div>
  <?php $content = ob_get_clean(); render_admin('admin_blocks_list.php', ['items'=>[],'content'=>$content,'title'=>'Logowanie']); exit;
}

if ($action === 'logout') { unset($_SESSION['admin']); redirect('/admin/?action=login'); }

ensure_logged();

if ($action === 'blocks' || $action === 'list') {
  $items = $blocks->all();
  render_admin('admin_blocks_list.php', compact('items') + ['title'=>'Bloki']);
  exit;
}

if ($action === 'edit') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  $block = $id ? ($blocks->find($id) ?? []) : ['id'=>0,'region'=>$regions[0],'title'=>'','body_html'=>'','position'=>0,'visible'=>1];
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_post_csrf();
    $data = [
      'id'       => $id ?: null,
      'region'   => in_array($_POST['region'] ?? '', $regions, true) ? $_POST['region'] : $regions[0],
      'title'    => trim($_POST['title'] ?? ''),
      'body_html'=> $_POST['body_html'] ?? '',
      'position' => (int)($_POST['position'] ?? 0),
      'visible'  => !empty($_POST['visible']) ? 1 : 0,
    ];
    $savedId = $blocks->save($data);
    redirect('/admin/?page=blocks');
  }
  render_admin('admin_block_form.php', compact('block','regions') + ['title'=> $id? 'Edycja':'Nowy']);
  exit;
}

if ($action === 'delete') {
  if (empty($_GET['csrf']) || $_GET['csrf'] !== ($_SESSION['csrf'] ?? '')) { http_response_code(400); echo 'Bad CSRF'; exit; }
  $id = (int)($_GET['id'] ?? 0);
  if ($id) $blocks->delete($id);
  redirect('/admin/?page=blocks');
}

if ($action === 'move') {
  $id = (int)($_GET['id'] ?? 0);
  $d  = (int)($_GET['d'] ?? 0);
  if ($id && $d) $blocks->move($id, $d);
  redirect('/admin/?page=blocks');
}

http_response_code(404); echo "Not found";
PHP

############################################
# 5) DB schema + seed (korekta Pani Marzeny)
############################################
cat > "$DBDIR/init.sql" <<'SQL'
PRAGMA journal_mode=WAL;
CREATE TABLE IF NOT EXISTS content_blocks (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  region TEXT NOT NULL,
  title TEXT,
  body_html TEXT NOT NULL,
  position INTEGER NOT NULL DEFAULT 0,
  visible INTEGER NOT NULL DEFAULT 1,
  updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_blocks_region_pos ON content_blocks(region, position);

CREATE TABLE IF NOT EXISTS nav_items(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  label TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  visible INTEGER NOT NULL DEFAULT 1,
  ord INTEGER NOT NULL DEFAULT 0
);
SQL

# utwórz/zmigruj DB
if command -v sqlite3 >/dev/null 2>&1; then
  sqlite3 "$DBDIR/tjw.sqlite" < "$DBDIR/init.sql"
  # wyczyść i zasiej treści wg korekty
  sqlite3 "$DBDIR/tjw.sqlite" <<'SQL'
DELETE FROM content_blocks;

INSERT INTO content_blocks(region,title,body_html,position,visible) VALUES
('home.misja','Misja',
'<p>Wspieram od wielu lat rodziców: ojców i matki doświadczających alienacji rodzicielskiej. Działam społecznie i <em>pro bono</em>. Jestem aktywistką na rzecz praw ojców i dzieci.</p>
<p>Podchodzę z zaangażowaniem i sercem, a jednocześnie racjonalnie. Jestem praktykiem od samego początku. Zaczęłam od pomocy kilku przyjaciołom narażonym niesłusznie na utratę więzi z dziećmi i wówczas odkryłam, jak wiele osób potrzebuje wsparcia w alienacji rodzicielskiej i jest pozostawionych samych sobie z ogromnym problemem.</p>
<p><strong>Moja dewiza: Dobro dziecka ponad spór.</strong></p>
<p>Pojęcie „dobra dziecka” rozumiem inaczej niż nic nieznaczący slogan nadużywany w sądach. Dziecko i jego bezpieczne oraz szczęśliwe dzieciństwo z obojgiem rozwiedzionych rodziców jest centralnym podmiotem moich działań.</p>',0,1),

('home.jak_pomagam','Bezpłatnie',
'<ul class="list-disc list-inside space-y-1">
<li>konsultacje telefoniczne,</li>
<li>analiza dokumentów,</li>
<li>wskazywanie rozwiązań prawnych, organizacyjnych i komunikacyjnych,</li>
<li>plan działań krok po kroku,</li>
<li>analiza strategii procesowej,</li>
<li>przygotowanie do OZSS i następnie omówienie opinii biegłych,</li>
<li>wsparcie mentalne.</li>
</ul>',0,1),

('home.jak_pomagam','Odpłatnie',
'<p>mediacje dla rodziców — w przygotowaniu. Zamierzam zdobyć wkrótce certyfikat uprawniający do prowadzenia mediacji sądowych i pozasądowych, aby jeszcze skuteczniej pomagać rodzicom i dzieciom.</p>',1,1),

('home.szybka_pomoc','Szybka pomoc',
'<p class="text-sm">Umów godziną konsultację telefoniczną, żeby przeanalizować przypadek Twój i Twojego Dziecka. Pomagam poza godzinami mojej pracy zawodowej — wieczorami i w niektóre weekendy.</p>
<ul class="text-sm space-y-3 mt-3">
<li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full" style="background:#f97316"></span><span>Zapisuj daty niewykonanych kontaktów.</span></li>
<li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full" style="background:#f97316"></span><span>Nie zwlekaj — rozważ mediacje.</span></li>
<li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full" style="background:#f97316"></span><span>Dbaj o spójny przekaz dla dziecka.</span></li>
</ul>
<div class="mt-4 space-y-2">
  <a href="/#kontakt" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl text-white" style="background:#ea580c">Umów rozmowę</a>
  <a href="https://www.linkedin.com/in/marzena-ciupek-tarnawska-ba0124273/" target="_blank" rel="noopener" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl border" style="border-color:#fed7aa;color:#7c2d12">Napisz na LinkedIn</a>
</div>
<p class="text-xs text-slate-500 mt-3">Dziękuję i pozdrawiam.<br>Marzena Ciupek-Tarnawska</p>',0,1),

('home.dla_kogo','Dla kogo',
'<p>Dla rodziców i ich bliskich szukających wiedzy, wsparcia i konkretnych kroków „co dalej”. Niezależnie, czy sprawa sądowa jest w trakcie, czy po, czy są pierwsze sygnały, że konieczny będzie wybór drogi prawnej. Dla wszystkich rodziców spodziewających się rozwodu, czy rozstania w związku nieformalnym.</p>',0,1);
SQL
else
  echo "⚠️ sqlite3 nie znaleziono — pomiń initialize DB"
fi

############################################
# 6) INFO końcowe
############################################
echo "✅ Pliki nadpisane. DB zaktualizowana."
echo "🔐 Panel: http://tatajestwazny.local/admin/  (login: admin / hasło: changeme)"
echo "ℹ️ Zmień hasło edytując $ADM/auth.php (ADMIN_PASS_HASH)."
echo "🧹 Jeżeli SW zcache'ował starą wersję: DevTools → Application → Clear storage → Clear site data, albo zmień wersję CACHE w sw.js."
