<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <a href="/blog" class="inline-flex items-center gap-2 text-sm text-orange-600 font-medium">← Powrót do bloga</a>
  <header class="mt-6">
    <p class="text-sm uppercase tracking-[0.25em] text-orange-500 font-semibold">Historia</p>
    <h1 class="text-4xl font-semibold text-slate-900 leading-tight mt-3"><?= htmlspecialchars($post['title']) ?></h1>
    <?php if (!empty($post['published_at'])): ?>
      <p class="text-sm text-slate-500 mt-2">Opublikowano: <?= date('d.m.Y', strtotime($post['published_at'])) ?></p>
    <?php endif; ?>
  </header>
  <div class="prose prose-lg prose-orange max-w-none text-slate-800 mt-8">
    <?= $post['body_html'] ?>
  </div>
</article>
