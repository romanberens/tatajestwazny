<h2 class="text-xl font-semibold mb-4">Bloki treści</h2>
<div class="mb-4">
  <a class="inline-block px-3 py-2 rounded bg-emerald-600 text-white" href="/admin/?action=edit">+ Nowy blok</a>
</div>
<table class="w-full text-sm bg-white rounded shadow-soft overflow-hidden">
  <thead class="bg-slate-100 text-left">
    <tr><th class="p-2">ID</th><th class="p-2">Region</th><th class="p-2">Tytuł</th><th class="p-2">Pozycja</th><th class="p-2">Widoczny</th><th class="p-2">Akcje</th></tr>
  </thead>
  <tbody>
  <?php foreach ($items as $it): ?>
    <tr class="border-t">
      <td class="p-2"><?= (int)$it['id'] ?></td>
      <td class="p-2"><?= htmlspecialchars($it['region']) ?></td>
      <td class="p-2"><?= htmlspecialchars($it['title'] ?? '') ?></td>
      <td class="p-2"><?= (int)$it['position'] ?></td>
      <td class="p-2"><?= $it['visible'] ? 'tak' : 'nie' ?></td>
      <td class="p-2 flex gap-2">
        <a class="px-2 py-1 rounded border" href="/admin/?action=move&id=<?= (int)$it['id'] ?>&d=-1">↑</a>
        <a class="px-2 py-1 rounded border" href="/admin/?action=move&id=<?= (int)$it['id'] ?>&d=1">↓</a>
        <a class="px-2 py-1 rounded border" href="/admin/?action=edit&id=<?= (int)$it['id'] ?>">Edytuj</a>
        <a class="px-2 py-1 rounded border text-red-600" href="/admin/?action=delete&id=<?= (int)$it['id'] ?>&csrf=<?= htmlspecialchars($_SESSION['csrf']) ?>" onclick="return confirm('Usunąć?')">Usuń</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
