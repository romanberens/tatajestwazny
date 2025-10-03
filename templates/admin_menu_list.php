<header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
  <div>
    <h2 class="text-2xl font-semibold text-white">Menu nawigacyjne</h2>
    <p class="text-sm text-slate-400 mt-1">Kontroluj elementy widoczne w górnej nawigacji serwisu.</p>
  </div>
  <a class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-orange-500 text-white text-sm font-semibold shadow-soft hover:bg-orange-600 transition" href="/admin/?section=menu&action=new">+ Dodaj element</a>
</header>

<div class="overflow-hidden rounded-3xl border border-slate-800/60">
  <table class="w-full text-sm text-slate-200">
    <thead class="bg-slate-900/80 text-left uppercase tracking-wide text-xs">
      <tr>
        <th class="px-4 py-3">Etykieta</th>
        <th class="px-4 py-3">Typ</th>
        <th class="px-4 py-3">Cel</th>
        <th class="px-4 py-3">Pozycja</th>
        <th class="px-4 py-3">Widoczny</th>
        <th class="px-4 py-3 text-right">Akcje</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-800/60">
      <?php foreach ($items as $item): ?>
        <tr class="bg-slate-900/40">
          <td class="px-4 py-3 font-medium text-slate-100"><?= htmlspecialchars($item['label']) ?></td>
          <td class="px-4 py-3 text-slate-300"><?= $item['type'] === 'external' ? 'Zewnętrzny' : 'Wewnętrzny' ?></td>
          <td class="px-4 py-3 font-mono text-xs text-slate-400"><?= htmlspecialchars($item['target']) ?></td>
          <td class="px-4 py-3 text-slate-300"><?= (int) $item['position'] ?></td>
          <td class="px-4 py-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold <?= $item['visible'] ? 'bg-emerald-500/10 text-emerald-200 border border-emerald-400/30' : 'bg-slate-800 text-slate-400 border border-slate-700' ?>">
              <?= $item['visible'] ? 'Widoczny' : 'Ukryty' ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2">
              <a class="inline-flex items-center px-3 py-1.5 rounded-xl border border-slate-700 hover:border-orange-400/60 text-xs" href="/admin/?section=menu&action=edit&id=<?= (int) $item['id'] ?>">Edytuj</a>
              <form method="post" class="inline" onsubmit="return confirm('Usunąć ten element menu?');">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                <input type="hidden" name="action" value="delete_menu">
                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                <button class="px-3 py-1.5 rounded-xl border border-red-500/40 text-red-300 hover:bg-red-500/10 text-xs">Usuń</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <tr>
          <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">Brak elementów menu. Dodaj pierwszy link, aby użytkownicy mogli łatwiej nawigować.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
