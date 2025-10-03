<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <a href="/" class="inline-flex items-center gap-2 text-sm text-orange-600 font-medium">← Wróć na stronę główną</a>
  <header class="mt-6">
    <p class="text-sm uppercase tracking-[0.25em] text-orange-500 font-semibold">Podstrona</p>
    <h1 class="text-4xl font-semibold text-slate-900 leading-tight mt-3"><?= htmlspecialchars($page['title']) ?></h1>
  </header>
  <div class="prose prose-lg prose-orange max-w-none text-slate-800 mt-8">
    <?= $page['body_html'] ?>
  </div>
</article>
