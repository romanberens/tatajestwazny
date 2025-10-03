<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <header class="mb-10 flex items-center justify-between gap-4">
    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-orange-500 font-semibold">Blog</p>
      <h2 class="text-3xl font-semibold text-slate-900">Najnowsze artykuły i historie</h2>
    </div>
    <a href="/" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full border border-orange-200 text-orange-600 hover:bg-orange-50">← Wróć</a>
  </header>
  <div class="grid gap-6">
    <?php foreach ($posts as $post): ?>
      <article class="bg-white rounded-2xl shadow-soft p-6">
        <h3 class="text-2xl font-semibold text-slate-900">
          <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="hover:text-orange-600 transition"><?= htmlspecialchars($post['title']) ?></a>
        </h3>
        <?php if (!empty($post['published_at'])): ?>
          <p class="text-sm text-slate-500 mt-1">Opublikowano: <?= date('d.m.Y', strtotime($post['published_at'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($post['excerpt'])): ?>
          <p class="mt-4 text-slate-600 leading-relaxed"><?= htmlspecialchars($post['excerpt']) ?></p>
        <?php endif; ?>
        <a href="/blog/<?= htmlspecialchars($post['slug']) ?>" class="inline-flex items-center gap-2 text-orange-600 font-medium mt-4">Czytaj dalej<span aria-hidden="true">→</span></a>
      </article>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?>
      <p class="text-slate-600">Brak opublikowanych wpisów. Zaloguj się do panelu, aby dodać pierwszą historię.</p>
    <?php endif; ?>
  </div>
</section>
