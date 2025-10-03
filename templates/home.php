<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
  <!-- Misja -->
  <?php $misjaBlock = $misja[0] ?? null; ?>
  <section class="bg-white rounded-2xl p-6 shadow-soft mb-6">
    <h2 class="text-2xl font-semibold mb-3"><?= htmlspecialchars($misjaBlock['title'] ?? 'Misja') ?></h2>
    <div class="prose prose-orange max-w-none text-slate-800">
      <?= $misjaBlock['body_html'] ?? '<p>Uzupełnij treść w panelu.</p>' ?>
    </div>
  </section>

  <!-- Jak pomagam -->
  <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <article class="bg-white rounded-2xl p-6 shadow-soft lg:col-span-2">
      <h2 class="text-2xl font-semibold mb-3">Jak pomagam</h2>
      <?php foreach ($jak_pomagam as $b): ?>
        <div class="mb-5">
          <?php if (!empty($b['title'])): ?><h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($b['title']) ?></h3><?php endif; ?>
          <div class="prose max-w-none"><?= $b['body_html'] ?></div>
        </div>
      <?php endforeach; ?>
    </article>

    <!-- Szybka pomoc -->
    <?php $szybkaPomocBlock = $szybka_pomoc[0] ?? null; ?>
    <aside class="bg-white rounded-2xl p-6 shadow-soft">
      <h3 class="text-lg font-semibold mb-3"><?= htmlspecialchars($szybkaPomocBlock['title'] ?? 'Szybka pomoc') ?></h3>
      <div class="prose max-w-none"><?= $szybkaPomocBlock['body_html'] ?? '' ?></div>
    </aside>
  </section>

  <!-- Dla kogo -->
  <?php $dlaKogoBlock = $dla_kogo[0] ?? null; ?>
  <section class="bg-white rounded-2xl p-6 shadow-soft">
    <h2 class="text-2xl font-semibold mb-3"><?= htmlspecialchars($dlaKogoBlock['title'] ?? 'Dla kogo') ?></h2>
    <div class="prose max-w-none"><?= $dlaKogoBlock['body_html'] ?? '' ?></div>
  </section>
</main>
