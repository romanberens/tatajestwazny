<header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
  <div>
    <h2 class="text-2xl font-semibold text-white">Blog</h2>
    <p class="text-sm text-slate-400 mt-1">Publikuj historie i poradniki wspierające rodziców.</p>
  </div>
  <a class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-orange-500 text-white text-sm font-semibold shadow-soft hover:bg-orange-600 transition" href="/admin/?section=blog&action=new">+ Dodaj wpis</a>
</header>

<div class="overflow-hidden rounded-3xl border border-slate-800/60">
  <table class="w-full text-sm text-slate-200">
    <thead class="bg-slate-900/80 text-left uppercase tracking-wide text-xs">
      <tr>
        <th class="px-4 py-3">Tytuł</th>
        <th class="px-4 py-3">Slug</th>
        <th class="px-4 py-3">Publikacja</th>
        <th class="px-4 py-3">Aktualizacja</th>
        <th class="px-4 py-3 text-right">Akcje</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-800/60">
      <?php foreach ($posts as $post): ?>
        <tr class="bg-slate-900/40">
          <td class="px-4 py-3 font-medium text-slate-100"><?= htmlspecialchars($post['title']) ?></td>
          <td class="px-4 py-3 font-mono text-xs text-slate-400">/blog/<?= htmlspecialchars($post['slug']) ?></td>
          <td class="px-4 py-3 text-slate-300"><?= $post['published_at'] ? date('d.m.Y H:i', strtotime($post['published_at'])) : 'Szkic' ?></td>
          <td class="px-4 py-3 text-slate-300"><?= date('d.m.Y H:i', strtotime($post['updated_at'])) ?></td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2">
              <a class="inline-flex items-center px-3 py-1.5 rounded-xl border border-slate-700 hover:border-orange-400/60 text-xs" href="/admin/?section=blog&action=edit&id=<?= (int) $post['id'] ?>">Edytuj</a>
              <form method="post" class="inline" onsubmit="return confirm('Usunąć ten wpis?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="delete_post">
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                <button class="px-3 py-1.5 rounded-xl border border-red-500/40 text-red-300 hover:bg-red-500/10 text-xs">Usuń</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($posts)): ?>
        <tr>
          <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">Brak wpisów. Podziel się historiami, aby społeczność mogła czerpać wsparcie.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
