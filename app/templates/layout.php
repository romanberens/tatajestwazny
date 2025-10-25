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
<body class="bg-[#fff7ed] text-slate-800 antialiased min-h-screen flex flex-col">
  <?php $navItems = $navItems ?? []; ?>
  <header class="border-b border-orange-100/70 bg-gradient-to-r from-[#ffedd5] via-white to-[#fffbeb]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm uppercase tracking-[0.25em] text-orange-500 font-semibold">tata jest ważny</p>
          <h1 class="text-3xl sm:text-4xl font-semibold text-slate-900 mt-2">Wsparcie dla rodziców w kryzysie</h1>
          <p class="mt-2 max-w-xl text-slate-600">Rozmowa, plan działania i społeczność, która rozumie Twoją historię. Tu otrzymasz narzędzia do budowania obecności w życiu dziecka.</p>
        </div>
        <nav class="self-start md:self-center">
          <ul class="flex flex-wrap gap-3 text-sm font-medium text-slate-700">
            <li><a class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 shadow-soft hover:bg-white transition" href="/">🏠 Strona główna</a></li>
            <?php foreach ($navItems as $item): ?>
              <?php
                $href = $item['type'] === 'external' ? $item['target'] : '/' . ltrim($item['target'], '/');
                $icon = $item['type'] === 'external' ? '↗' : '→';
              ?>
              <li>
                <a class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 shadow-soft hover:bg-white transition" href="<?= htmlspecialchars($href) ?>" <?= $item['type'] === 'external' ? 'target="_blank" rel="noopener"' : '' ?>>
                  <?= htmlspecialchars($item['label']) ?>
                  <span class="text-orange-500 text-lg leading-none"><?= $icon ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="flex-1 w-full">
    <?= $content ?>
  </div>

  <footer class="border-t border-orange-100 py-8 mt-12 bg-white/70 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm text-slate-600 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <p>© <?= date('Y') ?> tatajestwazny.pl — Inicjatywa Marzeny Ciupek-Tarnawskiej</p>
      <p>Hosting i opieka technologiczna: <span class="font-medium">OneNetworks</span></p>
    </div>
  </footer>
  <script src="/main.js"></script>
</body>
</html>
