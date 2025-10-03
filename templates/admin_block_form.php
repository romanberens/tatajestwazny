<h2 class="text-xl font-semibold mb-4"><?= !empty($block['id']) ? 'Edycja bloku' : 'Nowy blok' ?></h2>
<form method="post" class="bg-white p-4 rounded shadow-soft space-y-3">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
  <label class="block">Region
    <select name="region" class="border p-2 rounded w-full">
      <?php foreach ($regions as $r): ?>
        <option value="<?= $r ?>" <?= ($block['region'] ?? '')===$r ? 'selected':'' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label class="block">Tytuł
    <input type="text" name="title" value="<?= htmlspecialchars($block['title'] ?? '') ?>" class="border p-2 rounded w-full">
  </label>
  <label class="block">Treść (HTML)
    <textarea name="body_html" rows="10" class="border p-2 rounded w-full"><?= htmlspecialchars($block['body_html'] ?? '') ?></textarea>
  </label>
  <div class="flex gap-4 items-center">
    <label>Pozycja <input type="number" name="position" value="<?= (int)($block['position'] ?? 0) ?>" class="border p-2 rounded w-24"></label>
    <label class="inline-flex items-center gap-2"><input type="checkbox" name="visible" value="1" <?= !empty($block['visible']) ? 'checked':'' ?>> Widoczny</label>
  </div>
  <div class="flex gap-2">
    <button class="px-4 py-2 rounded bg-brand-600 text-white" style="background:#ea580c">Zapisz</button>
    <a class="px-4 py-2 rounded border" href="/admin/?page=blocks">Anuluj</a>
  </div>
</form>
