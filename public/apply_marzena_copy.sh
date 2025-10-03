#!/bin/bash
set -euo pipefail

BASE="$HOME/projects/tatajestwazny/public"
SECT="$BASE/sections"

echo "📁 Projekt: $BASE"
test -d "$BASE" || { echo "❌ Brak katalogu $BASE"; exit 1; }

# 1) Kopie bezpieczeństwa (po 1 szt.)
ts=$(date +%Y%m%d-%H%M%S)
mkdir -p "$BASE/.backup/$ts"
cp -a "$BASE/index.php" "$BASE/.backup/$ts/" 2>/dev/null || true
cp -a "$SECT" "$BASE/.backup/$ts/" 2>/dev/null || true

# 2) Usuń sekcję Manifest z index.php i plik sekcji
if grep -q "sections/manifest.php" "$BASE/index.php"; then
  sed -i 's@^\s*<?php include('"'"'sections/manifest.php'"'"'); ?>\s*$@@' "$BASE/index.php"
fi
rm -f "$SECT/manifest.php"

# 3) Zaktualizuj sekcję HOME (pełne, zaakceptowane przez Panią Marzenę copy)
mkdir -p "$SECT"
cat > "$SECT/home.php" <<'PHP'
<section id="home" class="page" tabindex="-1" aria-labelledby="home-title">
  <h1 id="home-title" class="sr-only">Tata Jest Ważny — Start</h1>

  <!-- Misja -->
  <article class="bg-white rounded-2xl p-6 shadow-soft mb-6">
    <h2 class="text-2xl font-semibold mb-3">Misja</h2>
    <div class="prose prose-orange max-w-none text-slate-800">
      <p>Wspieram od wielu lat rodziców: ojców i matki doświadczających alienacji rodzicielskiej.
      Działam społecznie i <em>pro bono</em>. Jestem aktywistką na rzecz praw ojców i dzieci.</p>

      <p>Podchodzę z zaangażowaniem i sercem, a jednocześnie racjonalnie. Jestem praktykiem od samego początku.
      Zaczęłam od pomocy kilku przyjaciołom narażonym niesłusznie na utratę więzi z dziećmi i wówczas odkryłam, jak wiele osób potrzebuje wsparcia
      w alienacji rodzicielskiej i jest pozostawionych samych sobie z ogromnym problemem.</p>

      <p><strong>Moja dewiza: Dobro dziecka ponad spór.</strong></p>

      <p>Pojęcie „dobra dziecka” rozumiem inaczej niż nic nieznaczący slogan nadużywany w sądach.
      Dziecko i jego bezpieczne oraz szczęśliwe dzieciństwo z obojgiem rozwiedzionych rodziców jest centralnym podmiotem moich działań.</p>
    </div>
  </article>

  <!-- Jak pomagam -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <article class="bg-white rounded-2xl p-6 shadow-soft lg:col-span-2">
      <h2 class="text-2xl font-semibold mb-3">Jak pomagam</h2>

      <h3 class="text-lg font-semibold mt-2 mb-2">Bezpłatnie:</h3>
      <ul class="list-disc list-inside space-y-1 text-slate-800">
        <li>konsultacje telefoniczne,</li>
        <li>analiza dokumentów,</li>
        <li>wskazywanie rozwiązań prawnych, organizacyjnych i komunikacyjnych,</li>
        <li>plan działań krok po kroku,</li>
        <li>analiza strategii procesowej,</li>
        <li>przygotowanie do OZSS i następnie omówienie opinii biegłych,</li>
        <li>wsparcie mentalne.</li>
      </ul>

      <h3 class="text-lg font-semibold mt-4 mb-2">Odpłatnie:</h3>
      <p class="text-slate-800">
        mediacje dla rodziców — w przygotowaniu. Zamierzam zdobyć wkrótce certyfikat uprawniający do prowadzenia mediacji sądowych i pozasądowych,
        aby jeszcze skuteczniej pomagać rodzicom i dzieciom.
      </p>
    </article>

    <!-- Szybka pomoc (CTA) -->
    <aside class="bg-white rounded-2xl p-6 shadow-soft">
      <h3 class="text-lg font-semibold mb-3">Szybka pomoc</h3>
      <p class="text-sm text-slate-700 mb-3">
        Umów godziną konsultację telefoniczną, żeby przeanalizować przypadek Twój i Twojego Dziecka.
        Pomagam poza godzinami mojej pracy zawodowej — wieczorami i w niektóre weekendy.
      </p>
      <ul class="text-sm space-y-3">
        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500"></span><span>Zapisuj daty niewykonanych kontaktów.</span></li>
        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500"></span><span>Nie zwlekaj — rozważ mediacje.</span></li>
        <li class="flex gap-3"><span class="mt-1 h-2 w-2 rounded-full bg-brand-500"></span><span>Dbaj o spójny przekaz dla dziecka.</span></li>
      </ul>
      <p class="text-sm text-slate-700 mt-3">
        Gwarantuję podejście holistyczne i indywidualne. Nie prowadzę grup i warsztatów.
        Dzięki temu rozmawiamy konkretnie i dyskretnie. Wspieram rodziców w zależności od potrzeb: krótko- lub długoterminowo.
      </p>
      <div class="mt-5 space-y-2">
        <button type="button" onclick="navigate('kontakt')" class="w-full px-4 py-2 rounded-xl bg-brand-500 text-white hover:bg-brand-600">Umów rozmowę</button>
        <a href="https://www.linkedin.com/in/marzena-ciupek-tarnawska-ba0124273/" target="_blank" rel="noopener" class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-orange-200 text-brand-700">Napisz na LinkedIn</a>
      </div>
      <p class="text-xs text-slate-500 mt-4">Dziękuję i pozdrawiam.<br>Marzena Ciupek-Tarnawska</p>
    </aside>
  </div>

  <!-- Dla kogo -->
  <article class="bg-white rounded-2xl p-6 shadow-soft">
    <h2 class="text-2xl font-semibold mb-3">Dla kogo</h2>
    <p class="text-slate-800">
      Dla rodziców i ich bliskich szukających wiedzy, wsparcia i konkretnych kroków „co dalej”.
      Niezależnie, czy sprawa sądowa jest w trakcie, czy po, czy są pierwsze sygnały, że konieczny będzie wybór drogi prawnej.
      Dla wszystkich rodziców spodziewających się rozwodu, czy rozstania w związku nieformalnym.
    </p>
  </article>
</section>
PHP

# 4) BLOG: dopisek, że będą też wywiady
if [ -f "$SECT/blog.php" ]; then
  # dodaj lead, jeśli go nie ma
  if ! grep -q "wywiad" "$SECT/blog.php"; then
    sed -i 's@Posty z LinkedIn oraz własne materiały@Posty z LinkedIn, własne materiały oraz wywiady@g' "$SECT/blog.php"
  fi
else
  # minimalny blog jeśli nie istnieje
  cat > "$SECT/blog.php" <<'PHP'
<section id="blog" class="page hidden" tabindex="-1" aria-labelledby="blog-title">
  <div class="mb-6 flex items-center justify-between">
    <h2 id="blog-title" class="text-2xl font-semibold">Blog / Aktualności</h2>
    <span class="text-sm text-slate-500">Posty z LinkedIn, własne materiały oraz wywiady</span>
  </div>
  <p class="text-slate-700">Wkrótce: wywiady i materiały merytoryczne.</p>
</section>
PHP
fi

# 5) Usuń linki/akcje do nieistniejącej sekcji "manifest" w JS/HTML (w sekcji home CTA)
# (już nie mamy przycisku nawigującego do manifestu – upewnijmy się)
sed -i 's@navigate('"'"'manifest'"'"')@navigate('"'"'blog'"'"')@g' "$SECT/home.php" || true

echo "✅ Wgrano poprawki Pani Marzeny:"
echo "   • Usunięto sekcję 'Manifest'"
echo "   • Zaktualizowano treści: Misja / Jak pomagam / Dla kogo / Szybka pomoc"
echo "   • Blog: informacja o wywiadach"
echo "   • Backup: $BASE/.backup/$ts"
