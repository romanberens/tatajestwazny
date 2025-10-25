  <footer>
    <p>© 2025 Tata Jest Ważny</p>
  </footer>

  <script>
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('✅ Service Worker zarejestrowany:', reg.scope))
        .catch(err => console.error('❌ Błąd rejestracji SW:', err));
    }
  </script>


<script src="/main.js"></script>

</body>
</html>
