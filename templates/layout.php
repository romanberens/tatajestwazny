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
