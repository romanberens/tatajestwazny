<section class="space-y-8">
  <header>
    <h2 class="text-2xl font-semibold text-white">Witaj w panelu administracyjnym</h2>
    <p class="text-sm text-slate-400 mt-1">Poniżej znajdziesz szybki podgląd kluczowych treści oraz skróty do ostatnich działań.</p>
  </header>

  <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6">
      <p class="text-sm text-slate-400">Bloki treści</p>
      <p class="mt-3 text-3xl font-semibold text-white"><?= (int) ($stats['blocks'] ?? 0) ?></p>
      <a href="/admin/?section=blocks" class="mt-4 inline-flex items-center gap-2 text-sm text-orange-300">Zarządzaj<span aria-hidden="true">→</span></a>
    </article>
    <article class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6">
      <p class="text-sm text-slate-400">Podstrony</p>
      <p class="mt-3 text-3xl font-semibold text-white"><?= (int) ($stats['pages'] ?? 0) ?></p>
      <a href="/admin/?section=pages" class="mt-4 inline-flex items-center gap-2 text-sm text-orange-300">Zarządzaj<span aria-hidden="true">→</span></a>
    </article>
    <article class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6">
      <p class="text-sm text-slate-400">Menu</p>
      <p class="mt-3 text-3xl font-semibold text-white"><?= (int) ($stats['menu'] ?? 0) ?></p>
      <a href="/admin/?section=menu" class="mt-4 inline-flex items-center gap-2 text-sm text-orange-300">Zarządzaj<span aria-hidden="true">→</span></a>
    </article>
    <article class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6">
      <p class="text-sm text-slate-400">Wpisy blogowe</p>
      <p class="mt-3 text-3xl font-semibold text-white"><?= (int) ($stats['posts'] ?? 0) ?></p>
      <a href="/admin/?section=blog" class="mt-4 inline-flex items-center gap-2 text-sm text-orange-300">Zarządzaj<span aria-hidden="true">→</span></a>
    </article>
  </div>

  <section class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6 lg:p-8">
    <header class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h3 class="text-xl font-semibold text-white">Ostatnie wpisy</h3>
        <p class="text-sm text-slate-400">Podgląd trzech najnowszych artykułów na blogu.</p>
      </div>
      <a href="/admin/?section=blog" class="inline-flex items-center gap-2 text-sm text-orange-300">Przejdź do bloga<span aria-hidden="true">→</span></a>
    </header>

    <?php if (!empty($recentPosts)): ?>
      <ul class="space-y-4">
        <?php foreach ($recentPosts as $post): ?>
          <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800/60 pb-4 last:border-b-0 last:pb-0">
            <div>
              <p class="text-sm text-slate-400"><?= $post['published_at'] ? date('d.m.Y H:i', strtotime($post['published_at'])) : 'Szkic' ?></p>
              <h4 class="text-lg font-semibold text-white mt-1"><?= htmlspecialchars($post['title']) ?></h4>
              <p class="text-sm text-slate-400">/blog/<?= htmlspecialchars($post['slug']) ?></p>
            </div>
            <a href="/admin/?section=blog&action=edit&id=<?= (int) $post['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 text-sm">Edytuj</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-sm text-slate-400">Brak opublikowanych wpisów. Dodaj pierwszy artykuł, aby rozpocząć komunikację z odbiorcami.</p>
    <?php endif; ?>
  </section>
</section>
