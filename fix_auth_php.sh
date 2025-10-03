#!/bin/bash
# 📄 Zastępuje auth.php parserem .env i poprawnym logowaniem

AUTH_FILE="public/admin/blog/auth.php"

cat > "$AUTH_FILE" << 'EOF'
<?php
session_start();

// ✅ Wczytaj plik .env (bez zależności zewnętrznych)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // komentarze
        if (!str_contains($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\""); // usuwa cudzysłowy i białe znaki
        $_ENV[$name] = $value;
    }
}

// ✅ Wczytaj hash hasła
$hash = $_ENV['AUTH_PASSWORD_HASH'] ?? '';

if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (password_verify($_POST['password'], $hash)) {
            $_SESSION['logged_in'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Nieprawidłowe hasło!";
        }
    }

    echo '<form method="post"><h2>🔐 Logowanie</h2>';
    if (isset($error)) echo "<p style=\"color:red\">$error</p>";
    echo '<input type="password" name="password" placeholder="Hasło">';
    echo '<button type="submit">Zaloguj</button></form>';
    exit;
}
?>
EOF

echo "✅ auth.php został zaktualizowany z wbudowanym parserem .env"
