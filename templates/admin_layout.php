<?php /** @var string $title */ /** @var string $content */ ?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Panel administracyjny • <?= htmlspecialchars($title ?? '') ?></title>
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>
<style>
  :focus-visible{outline:3px solid #fb923c;outline-offset:2px}
  .shadow-soft{box-shadow:0 20px 45px -20px rgba(15,23,42,.45)}
</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
<div class="min-h-screen w-full flex">
  <?php $activeSection = $activeSection ?? 'dashboard'; ?>
  <aside class="hidden lg:flex w-72 bg-slate-950/70 backdrop-blur border-r border-slate-800/60 flex-col justify-between">
    <div>
      <div class="px-6 py-6">
        <p class="text-xs uppercase tracking-[0.3em] text-orange-500 font-semibold">tatajestwazny.pl</p>
        <h1 class="text-2xl font-semibold mt-4">Panel administracyjny</h1>
        <p class="text-sm text-slate-400 mt-2">Zarządzaj treściami, menu i wpisami blogowymi w jednym miejscu.</p>
      </div>
      <nav class="px-3 space-y-1">
        <?php foreach (($nav ?? []) as $item): ?>
          <?php $isActive = ($item['id'] ?? '') === $activeSection; ?>
          <a href="<?= htmlspecialchars($item['href']) ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-medium transition <?= $isActive ? 'bg-orange-500 text-white shadow-soft' : 'text-slate-300 hover:bg-slate-800/60' ?>">
            <span><?= $item['icon'] ?? '•' ?></span>
            <span><?= htmlspecialchars($item['label'] ?? '') ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div class="px-6 py-6 text-xs text-slate-500 border-t border-slate-800/60">
      <p>Jesteś zalogowany jako administrator.</p>
      <p class="mt-2"><a href="/admin/auth.php?action=logout" class="text-orange-400 hover:text-orange-300">Wyloguj się</a></p>
    </div>
  </aside>

  <div class="flex-1 bg-slate-900">
    <header class="lg:hidden px-4 py-4 border-b border-slate-800/60">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-orange-500 font-semibold">Panel</p>
          <h2 class="text-lg font-semibold text-white"><?= htmlspecialchars($title ?? 'Panel administracyjny') ?></h2>
        </div>
        <a href="/admin/auth.php?action=logout" class="text-sm text-orange-400">Wyloguj</a>
      </div>
    </header>

    <main class="px-4 sm:px-6 lg:px-10 py-8">
      <?php if (!empty($flash)): ?>
        <div class="mb-6 rounded-2xl border border-emerald-400/40 bg-emerald-500/10 text-emerald-100 px-4 py-3 flex items-start gap-3">
          <span class="text-xl">✅</span>
          <p class="text-sm leading-6"><?= htmlspecialchars($flash) ?></p>
        </div>
      <?php endif; ?>
      <?php if (!empty($formError)): ?>
        <div class="mb-6 rounded-2xl border border-red-400/40 bg-red-500/10 text-red-100 px-4 py-3 flex items-start gap-3">
          <span class="text-xl">⚠️</span>
          <p class="text-sm leading-6"><?= htmlspecialchars($formError) ?></p>
        </div>
      <?php endif; ?>
      <div class="bg-slate-900/40 backdrop-blur rounded-3xl border border-slate-800/60 p-6 lg:p-8 text-slate-100">
        <?= $content ?>
      </div>
    </main>
  </div>
</div>
</body>
</html>
