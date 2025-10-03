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
