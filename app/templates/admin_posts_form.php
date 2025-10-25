<header class="mb-6">
  <h2 class="text-2xl font-semibold text-white"><?= !empty($post['id']) ? 'Edycja wpisu' : 'Nowy wpis na blogu' ?></h2>
  <p class="text-sm text-slate-400 mt-1">Buduj relacje z czytelnikami, publikując aktualności i historie.</p>
</header>

<form method="post" class="space-y-5" action="/admin/?section=blog">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="action" value="save_post">
  <?php if (!empty($post['id'])): ?>
    <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
  <?php endif; ?>

  <label class="block text-sm font-medium text-slate-300">Tytuł wpisu
    <input type="text" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
  </label>

  <div class="grid gap-5 md:grid-cols-2">
    <label class="block text-sm font-medium text-slate-300">Slug (adres URL)
      <div class="mt-2 flex rounded-2xl border border-slate-700 bg-slate-900/60 overflow-hidden">
        <span class="px-4 py-3 text-slate-500 text-sm">/blog/</span>
        <input type="text" name="slug" value="<?= htmlspecialchars($post['slug'] ?? '') ?>" class="flex-1 px-4 py-3 bg-transparent text-slate-100 focus:border-none focus:ring-0" required>
      </div>
    </label>
    <label class="block text-sm font-medium text-slate-300">Data publikacji (opcjonalnie)
      <input type="datetime-local" name="published_at" value="<?= !empty($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '' ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400">
    </label>
  </div>

  <label class="block text-sm font-medium text-slate-300">Krótki opis (excerpt)
    <textarea name="excerpt" rows="3" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" placeholder="W kilku zdaniach opisz temat wpisu."><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
  </label>

  <label class="block text-sm font-medium text-slate-300">Treść (HTML)
    <textarea name="body_html" rows="16" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required><?= htmlspecialchars($post['body_html'] ?? '') ?></textarea>
  </label>

  <div class="flex flex-wrap gap-3">
    <button class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition">Zapisz wpis</button>
    <a class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 transition" href="/admin/?section=blog">Anuluj</a>
  </div>
</form>
