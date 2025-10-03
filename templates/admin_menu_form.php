<header class="mb-6">
  <h2 class="text-2xl font-semibold text-white"><?= !empty($item['id']) ? 'Edycja elementu menu' : 'Nowy element menu' ?></h2>
  <p class="text-sm text-slate-400 mt-1">Dodaj link do podstrony lub zasobu zewnętrznego. Elementy są wyświetlane w kolejności rosnącej.</p>
</header>

<form method="post" class="space-y-5" action="/admin/?section=menu">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <input type="hidden" name="action" value="save_menu">
  <?php if (!empty($item['id'])): ?>
    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
  <?php endif; ?>

  <label class="block text-sm font-medium text-slate-300">Etykieta
    <input type="text" name="label" value="<?= htmlspecialchars($item['label'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
  </label>

  <div class="grid gap-5 md:grid-cols-2">
    <label class="block text-sm font-medium text-slate-300">Typ linku
      <select name="type" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400">
        <option value="internal" <?= ($item['type'] ?? '') === 'external' ? '' : 'selected' ?>>Wewnętrzny (podstrona)</option>
        <option value="external" <?= ($item['type'] ?? '') === 'external' ? 'selected' : '' ?>>Zewnętrzny (nowa karta)</option>
      </select>
    </label>
    <label class="block text-sm font-medium text-slate-300">Pozycja
      <input type="number" name="position" value="<?= (int) ($item['position'] ?? 0) ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400">
    </label>
  </div>

  <label class="block text-sm font-medium text-slate-300">Adres docelowy
    <input type="text" name="target" value="<?= htmlspecialchars($item['target'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" placeholder="np. o-nas lub https://link.pl" required>
  </label>

  <label class="inline-flex items-center gap-3 text-sm text-slate-300">
    <input type="checkbox" name="visible" value="1" class="rounded border-slate-700 bg-slate-900" <?= !empty($item['visible']) ? 'checked' : '' ?>>
    Widoczny w nawigacji
  </label>

  <div class="flex flex-wrap gap-3">
    <button class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition">Zapisz element</button>
    <a class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 transition" href="/admin/?section=menu">Anuluj</a>
  </div>
</form>
