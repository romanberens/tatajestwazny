<header class="mb-6">
  <h2 class="text-2xl font-semibold text-white"><?= !empty($block['id']) ? 'Edycja bloku' : 'Nowy blok treści' ?></h2>
  <p class="text-sm text-slate-400 mt-1">Wypełnij treść sekcji i zdecyduj, w którym miejscu pojawi się na stronie głównej.</p>
</header>

<form method="post" class="space-y-5" action="/admin/?section=blocks">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="action" value="save_block">
  <?php if (!empty($block['id'])): ?>
    <input type="hidden" name="id" value="<?= (int) $block['id'] ?>">
  <?php endif; ?>

  <div class="grid gap-5 md:grid-cols-2">
    <label class="block text-sm font-medium text-slate-300">Region
      <select name="region" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400">
        <?php foreach ($regions as $r): ?>
          <option value="<?= htmlspecialchars($r) ?>" <?= ($block['region'] ?? '') === $r ? 'selected' : '' ?>><?= htmlspecialchars($r) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="block text-sm font-medium text-slate-300">Pozycja
      <input type="number" name="position" value="<?= (int) ($block['position'] ?? 0) ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400">
    </label>
  </div>

  <label class="block text-sm font-medium text-slate-300">Tytuł sekcji
    <input type="text" name="title" value="<?= htmlspecialchars($block['title'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" placeholder="np. Nasza misja">
  </label>

  <label class="block text-sm font-medium text-slate-300">Treść (HTML)
    <textarea name="body_html" rows="12" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" placeholder="<p>...</p>"><?= htmlspecialchars($block['body_html'] ?? '') ?></textarea>
  </label>

  <label class="inline-flex items-center gap-3 text-sm text-slate-300">
    <input type="checkbox" name="visible" value="1" class="rounded border-slate-700 bg-slate-900" <?= !empty($block['visible']) ? 'checked' : '' ?>>
    Blok widoczny na stronie
  </label>

  <div class="flex flex-wrap gap-3">
    <button class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition">Zapisz blok</button>
    <a class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 transition" href="/admin/?section=blocks">Anuluj</a>
  </div>
</form>
