<?php
require_once __DIR__ . "/../includes/auth.php";

$envPath = __DIR__ . '/../../.env';
$envVars = parse_ini_file($envPath);
$currentHash = $envVars['AUTH_PASSWORD_HASH'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    if (!password_verify($old, $currentHash)) {
        $error = "❌ Błędne stare hasło.";
    } elseif ($new !== $repeat) {
        $error = "❌ Nowe hasła się nie zgadzają.";
    } elseif (strlen($new) < 8) {
        $error = "❌ Nowe hasło musi mieć co najmniej 8 znaków.";
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $lines = file($envPath);
        foreach ($lines as &$line) {
            if (str_starts_with($line, 'AUTH_PASSWORD_HASH=')) {
                $line = "AUTH_PASSWORD_HASH=\"$newHash\"\n";
            }
        }
        file_put_contents($envPath, implode('', $lines));
        $success = "✅ Hasło zostało zmienione.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Zmień hasło administratora</title>
  <link rel="stylesheet" href="/style.css">
</head>
<body>
  <h2>🔑 Zmień hasło administratora</h2>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
  <form method="post">
    <p>Stare hasło: <input type="password" name="old_password" required></p>
    <p>Nowe hasło: <input type="password" name="new_password" required></p>
    <p>Powtórz nowe hasło: <input type="password" name="repeat_password" required></p>
    <button type="submit">💾 Zapisz nowe hasło</button>
  </form>
  <p><a href="/admin/blog/index.php">⬅️ Powrót do panelu</a></p>
</body>
</html>
