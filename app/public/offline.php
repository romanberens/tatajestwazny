<?php require __DIR__ . '/../src/bootstrap.php'; ?>
<?php ob_start(); ?>
<main class="max-w-xl mx-auto pt-24 pb-12 text-center">
  <h1 class="text-2xl font-semibold">Jesteś offline 📴</h1>
  <p class="mt-3 text-slate-600">Brak połączenia z Internetem. Spróbuj ponownie później.</p>
  <a href="/" class="mt-5 inline-block px-4 py-2 rounded-xl text-white" style="background:#ea580c">Wróć do strony głównej</a>
</main>
<?php $content = ob_get_clean(); $title='Offline'; include __DIR__ . '/../templates/layout.php'; ?>
