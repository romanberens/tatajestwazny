<header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
  <div>
    <h2 class="text-2xl font-semibold text-white">Bloki treści</h2>
    <p class="text-sm text-slate-400 mt-1">Zarządzaj modułami wykorzystywanymi na stronie głównej.</p>
  </div>
  <a class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-orange-500 text-white text-sm font-semibold shadow-soft hover:bg-orange-600 transition" href="/admin/?section=blocks&action=new">+ Dodaj blok</a>
</header>

<div class="overflow-hidden rounded-3xl border border-slate-800/60">
  <table class="w-full text-sm text-slate-200">
    <thead class="bg-slate-900/80 text-left uppercase tracking-wide text-xs">
      <tr>
        <th class="px-4 py-3">ID</th>
        <th class="px-4 py-3">Region</th>
        <th class="px-4 py-3">Tytuł</th>
        <th class="px-4 py-3">Pozycja</th>
        <th class="px-4 py-3">Widoczny</th>
        <th class="px-4 py-3 text-right">Akcje</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-800/60">
    <?php foreach ($items as $it): ?>
      <tr class="bg-slate-900/40">
        <td class="px-4 py-3 font-mono text-xs text-slate-400">#<?= (int) $it['id'] ?></td>
        <td class="px-4 py-3 font-medium text-slate-200"><?= htmlspecialchars($it['region']) ?></td>
        <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($it['title'] ?? '') ?></td>
        <td class="px-4 py-3 text-slate-300"><?= (int) $it['position'] ?></td>
        <td class="px-4 py-3">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold <?= $it['visible'] ? 'bg-emerald-500/10 text-emerald-200 border border-emerald-400/30' : 'bg-slate-800 text-slate-400 border border-slate-700' ?>">
            <?= $it['visible'] ? 'Widoczny' : 'Ukryty' ?>
          </span>
        </td>
        <td class="px-4 py-3">
          <div class="flex items-center justify-end gap-2">
            <form method="post" class="inline">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
              <input type="hidden" name="action" value="move_block">
              <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
              <input type="hidden" name="delta" value="-1">
              <button class="px-3 py-1.5 rounded-xl border border-slate-700 hover:border-orange-400/60 text-xs">↑</button>
            </form>
            <form method="post" class="inline">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
              <input type="hidden" name="action" value="move_block">
              <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
              <input type="hidden" name="delta" value="1">
              <button class="px-3 py-1.5 rounded-xl border border-slate-700 hover:border-orange-400/60 text-xs">↓</button>
            </form>
            <a class="inline-flex items-center px-3 py-1.5 rounded-xl border border-slate-700 hover:border-orange-400/60 text-xs" href="/admin/?section=blocks&action=edit&id=<?= (int) $it['id'] ?>">Edytuj</a>
            <form method="post" class="inline" onsubmit="return confirm('Czy na pewno usunąć ten blok?');">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
              <input type="hidden" name="action" value="delete_block">
              <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
              <button class="px-3 py-1.5 rounded-xl border border-red-500/40 text-red-300 hover:bg-red-500/10 text-xs">Usuń</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($items)): ?>
      <tr>
        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">Brak bloków. Dodaj pierwszy, aby wypełnić stronę główną treścią.</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
