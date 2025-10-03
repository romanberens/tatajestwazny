<?php
session_start();
$hash = getenv('AUTH_PASSWORD_HASH') ?: (getenv('AUTH_PASSWORD_HASH') ?? '');

if (!$hash) {
  die('❌ Brakuje zmiennej środowiskowej AUTH_PASSWORD_HASH');
}

if (!isset($_SESSION['logged_in'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (password_verify($_POST['password'], $hash)) {
      $_SESSION['logged_in'] = true;
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    } else {
      $error = 'Nieprawidłowe hasło!';
    }
  }

  echo '<form method="post"><h2>🔐 Logowanie</h2>';
  if (isset($error)) echo '<p style="color:red">'.$error.'</p>';
  echo '<input type="password" name="password" placeholder="Hasło">';
  echo '<button type="submit">Zaloguj</button></form>';
  exit;
}
?>
