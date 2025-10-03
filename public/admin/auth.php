<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['password'] === 'tajnehaslo') {
    $_SESSION['logged_in'] = true;
    header('Location: /admin/');
    exit;
  } else {
    $error = "Błędne hasło.";
  }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
  session_destroy();
  header('Location: /admin/auth.php');
  exit;
}
?>
<!DOCTYPE html><html lang="pl"><head>
<meta charset="UTF-8"><title>Logowanie</title>
<style>
  body { font-family: sans-serif; max-width: 400px; margin: 4em auto; }
  form { display: flex; flex-direction: column; gap: 1em; }
</style>
</head><body>
<h1>Zaloguj się do panelu</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <input type="password" name="password" placeholder="Hasło" required>
  <button type="submit">Zaloguj</button>
</form>
</body></html>
