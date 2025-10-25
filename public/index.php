<?php
// ===============================================
//  TATAJESTWAZNY.PL – NOWA ODSŁONA STRONY
//  Aktualizacja: 23.10.2025
// ===============================================

date_default_timezone_set('Europe/Warsaw');
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TataJestWazny.pl – Nowa odsłona w drodze</title>
  <meta name="description" content="Nowa wersja TataJestWazny.pl już wkrótce. Projekt wspierający rodziców – szczególnie ojców – w walce o relacje z dziećmi i równe prawa rodzicielskie." />
  <meta property="og:title" content="TataJestWazny.pl – Strona w przygotowaniu" />
  <meta property="og:description" content="Projekt OneNetworks wspierający ojców i rodziny. Pozostań na bieżąco." />
  <meta property="og:image" content="https://onenetworks.pl/logo-wnb.jpg" />
  <meta property="og:type" content="website" />
  <link rel="icon" href="https://onenetworks.pl/favicon.ico" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in { animation: fadeIn 1s ease forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    .delay-4 { animation-delay: 0.8s; }

    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 10px rgba(250, 204, 21, 0.2); }
      50% { box-shadow: 0 0 25px rgba(250, 204, 21, 0.4); }
    }
    .glow { animation: pulseGlow 2s infinite; }
  </style>
</head>
<body class="bg-black text-gray-100 flex flex-col min-h-screen items-center justify-between font-sans overflow-x-hidden">

  <!-- Sekcja główna -->
  <main class="flex flex-col items-center text-center px-6 mt-20 mb-10 fade-in delay-1 max-w-3xl">

    <!-- Logo -->
    <a href="https://onenetworks.pl" target="_blank" rel="noopener noreferrer" class="fade-in delay-1 mb-6">
      <img src="https://onenetworks.pl/logo-wnb.jpg"
           alt="Logo OneNetworks"
           class="w-28 mx-auto mb-3 rounded-xl shadow-lg transition-transform duration-500 hover:scale-110">
    </a>

    <!-- Nagłówek -->
    <h1 class="text-4xl md:text-5xl font-bold tracking-wide mb-4 fade-in delay-2">
      TataJestWazny.pl
    </h1>
    <p class="text-lg text-gray-400 mb-8 fade-in delay-3">
      Nowa odsłona w drodze. To nie tylko strona – to misja.
    </p>

    <!-- Pasek symbolicznego postępu -->
    <div class="w-64 md:w-80 h-3 bg-gray-700 rounded-full overflow-hidden mb-8 fade-in delay-3">
      <div class="h-full bg-yellow-400 transition-all duration-1000" style="width: 80%;"></div>
    </div>
    <p class="text-sm text-gray-400 fade-in delay-3">
      Postęp prac: <span class="text-yellow-400 font-semibold">80%</span>
    </p>

    <!-- Cytat / manifest -->
    <blockquote class="italic text-gray-300 border-l-4 border-yellow-500 pl-5 mt-10 mb-8 text-left fade-in delay-4">
      „Każde dziecko potrzebuje obojga rodziców — niezależnie od ich historii.”
    </blockquote>

    <!-- Sekcja opisu -->
    <section class="text-left leading-relaxed fade-in delay-4">
      <p class="mb-4 text-gray-300">
        Wspieram od wielu lat rodziców: ojców i matki doświadczających alienacji rodzicielskiej.
        Działam społecznie i pro bono. Jestem aktywistką na rzecz praw ojców i dzieci.
      </p>

      <p class="mb-4 text-gray-300">
        Pojęcie „dobra dziecka” rozumiem nie jako slogan, ale jako realną troskę o jego
        bezpieczeństwo, miłość i możliwość dorastania z obojgiem rodziców – nawet jeśli się rozstali.
      </p>

      <p class="text-gray-300">
        Nowa wersja strony to przestrzeń wiedzy, wsparcia i dialogu. 
        Miejsce dla tych, którzy szukają odpowiedzi, narzędzi i motywacji, by działać dla dobra swoich dzieci.
      </p>

      <p class="mt-6 font-semibold text-right">
        <a href="https://www.linkedin.com/in/marzena-ciupek-tarnawska-ba0124273/"
           target="_blank"
           rel="noopener noreferrer"
           class="text-blue-400 hover:text-blue-300 underline transition">
          — Marzena Ciupek-Tarnawska
        </a>
      </p>
    </section>

    <!-- Sekcja z linkiem do działającej aplikacji -->
    <section class="mt-16 mb-12 fade-in delay-3 w-full max-w-2xl px-6">
      <div class="rounded-2xl bg-gradient-to-r from-yellow-500 to-yellow-400 text-black p-8 text-center shadow-2xl border border-yellow-300 glow">
        <h2 class="text-2xl font-extrabold mb-3 drop-shadow">🚀 Serwis już działa!</h2>
        <p class="text-sm mb-6 font-medium leading-relaxed">
          Aplikacja <strong>TataJestWazny.pl</strong> jest dostępna online.  
          Możesz ją dodać na swój telefon lub komputer i korzystać z niej jak z aplikacji.
        </p>
        <a href="https://app.tatajestwazny.pl/"
           target="_blank"
           rel="noopener noreferrer"
           class="inline-block bg-black text-yellow-400 font-semibold py-3 px-8 rounded-full shadow-lg 
                  transition-transform duration-300 hover:scale-110 hover:bg-gray-900 hover:text-yellow-300">
           👉 Przejdź do aplikacji
        </a>
      </div>
    </section>

    <!-- Aktualizacja projektu -->
    <section class="bg-gray-900 rounded-xl p-6 mt-12 text-left leading-relaxed shadow-lg border border-gray-800 fade-in delay-4 w-full max-w-2xl">
      <h3 class="text-2xl font-bold mb-3 text-yellow-400">🗓️ Aktualizacja projektu – 23.10.2025</h3>
      <p class="text-gray-300 mb-3">
        Uruchomiono środowisko hostingu i aplikację w wersji publicznej.
        Trwają prace nad błędami panelu administracyjnego oraz przygotowaniem treści do publikacji.
      </p>
      <p class="text-gray-300 mb-3">
        Opracowywane są scenariusze automatyzacji publikacji i uzupełnianie witryny informacjami.
      </p>
      <p class="text-gray-300 mb-3">
        Aby pozostać na bieżąco z postępami projektu, śledź profil na LinkedIn:
      </p>
      <a href="https://www.linkedin.com/in/marzena-ciupek-tarnawska-ba0124273/"
         target="_blank"
         rel="noopener noreferrer"
         class="text-yellow-400 hover:text-yellow-300 font-semibold underline">
        ⭐ Marzena Ciupek-Tarnawska na LinkedIn
      </a>
    </section>
  </main>

  <!-- Stopka -->
  <footer class="w-full text-center py-6 text-xs text-gray-500 border-t border-gray-800 fade-in delay-4">
    © <?= $currentYear ?> TataJestWazny.pl · Projekt OneNetworks
  </footer>

</body>
</html>
