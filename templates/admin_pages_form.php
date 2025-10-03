<header class="mb-6">
  <h2 class="text-2xl font-semibold text-white"><?= !empty($page['id']) ? 'Edycja podstrony' : 'Nowa podstrona' ?></h2>
  <p class="text-sm text-slate-400 mt-1">Podstrony świetnie sprawdzają się jako regulaminy, landing page’e i informacje stałe.</p>
</header>

<form method="post" class="space-y-5" action="/admin/?section=pages">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="action" value="save_page">
  <?php if (!empty($page['id'])): ?>
    <input type="hidden" name="id" value="<?= (int) $page['id'] ?>">
  <?php endif; ?>

  <label class="block text-sm font-medium text-slate-300">Tytuł
    <input type="text" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
  </label>

  <label class="block text-sm font-medium text-slate-300">Slug (adres URL)
    <div class="mt-2 flex rounded-2xl border border-slate-700 bg-slate-900/60 overflow-hidden">
      <span class="px-4 py-3 text-slate-500 text-sm">/</span>
      <input type="text" name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" class="flex-1 px-4 py-3 bg-transparent text-slate-100 focus:border-none focus:ring-0" placeholder="np. o-nas" required>
    </div>
  </label>

  <label class="block text-sm font-medium text-slate-300">Treść (HTML)
    <textarea name="body_html" rows="14" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required><?= htmlspecialchars($page['body_html'] ?? '') ?></textarea>
  </label>

  <div class="flex flex-wrap gap-3">
    <button class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition">Zapisz podstronę</button>
    <a class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 transition" href="/admin/?section=pages">Anuluj</a>
  </div>
</form>
