<section class="space-y-6">
  <header>
    <h2 class="text-2xl font-semibold text-white">Ustawienia administratora</h2>
    <p class="text-sm text-slate-400 mt-1">Zadbaj o bezpieczeństwo panelu poprzez regularną zmianę hasła.</p>
  </header>

  <div class="rounded-3xl border border-slate-800/60 bg-slate-900/50 p-6 lg:p-8">
    <h3 class="text-lg font-semibold text-white mb-4">Zmień hasło</h3>
    <form method="post" class="space-y-5" action="/admin/?section=settings">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
      <input type="hidden" name="action" value="update_password">

      <label class="block text-sm font-medium text-slate-300">Obecne hasło
        <input type="password" name="old_password" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
      </label>

      <div class="grid gap-5 md:grid-cols-2">
        <label class="block text-sm font-medium text-slate-300">Nowe hasło
          <input type="password" name="new_password" minlength="8" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
        </label>
        <label class="block text-sm font-medium text-slate-300">Powtórz nowe hasło
          <input type="password" name="repeat_password" minlength="8" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-slate-100 focus:border-orange-400" required>
        </label>
      </div>

      <p class="text-xs text-slate-400">Hasło powinno mieć minimum 8 znaków i zawierać mieszankę liter oraz cyfr.</p>

      <div class="flex flex-wrap gap-3">
        <button class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition">Zapisz nowe hasło</button>
        <a class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-700 text-slate-200 hover:border-orange-400/60 transition" href="/admin/">Powrót do panelu</a>
      </div>
    </form>
  </div>
</section>
