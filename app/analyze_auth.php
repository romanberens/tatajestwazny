<?php
/**
 * 🔍 TataJestWazny - analiza mechanizmu logowania
 * Autor: Roman Berens - OneNetworks
 */

$root = __DIR__;
$patterns = [
    'password_verify',
    'password_hash',
    '$_POST',
    'admin_password',
    'password',
    'auth',
    'verify'
];

echo "🔎 Analiza mechanizmu autoryzacji w $root\n\n";

// 1️⃣ Przeszukanie plików PHP
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$found = [];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    $content = file_get_contents($file);
    foreach ($patterns as $p) {
        if (stripos($content, $p) !== false) {
            $found[$file][] = $p;
        }
    }
}

if (empty($found)) {
    echo "❌ Nie znaleziono odniesień do haseł.\n";
    exit;
}

echo "📂 Znaleziono potencjalne pliki powiązane z logowaniem:\n";
foreach ($found as $file => $matches) {
    echo " - $file (" . implode(", ", array_unique($matches)) . ")\n";
}

// 2️⃣ Weryfikacja, czy istnieje plik konfiguracyjny z hasłem
$configFile = "$root/config/config.php";
if (file_exists($configFile)) {
    echo "\n⚙️  Wykryto plik konfiguracyjny: $configFile\n";
    $configContent = file_get_contents($configFile);
    if (preg_match('/admin_password[\'"]?\s*=>\s*[\'"](.+?)[\'"]/', $configContent, $m)) {
        echo "✅ Hasło zapisane w konfiguracji jako hash bcrypt:\n";
        echo "   {$m[1]}\n";
        echo "\nℹ️  Możesz wygenerować nowe hasło poleceniem:\n";
        echo "   docker exec -it tjw_php php -r 'echo password_hash(\"NOWE_HASLO\", PASSWORD_BCRYPT).\"\\n\";'\n";
        echo "\n   Następnie wklej wynik do $configFile w miejsce dotychczasowego hasha.\n";
    } else {
        echo "⚠️  Plik config.php istnieje, ale nie znaleziono klucza 'admin_password'.\n";
        echo "   Możesz dodać go ręcznie np.:\n";
        echo "   return ['admin_password' => 'HASH_TUTAJ'];\n";
    }
} else {
    echo "\n⚠️  Nie znaleziono pliku config.php.\n";
    echo "   Możliwe, że hasło jest w bazie danych SQLite lub w include auth.php.\n";
}

// 3️⃣ Sprawdzenie bazy danych
$dbFile = "$root/db/tjw.sqlite";
if (file_exists($dbFile)) {
    echo "\n🗄️  Sprawdzam bazę danych SQLite...\n";
    try {
        $pdo = new PDO("sqlite:$dbFile");
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        if ($tables) {
            echo "   Tabele: " . implode(", ", $tables) . "\n";
            if (in_array('admins', $tables)) {
                $cols = $pdo->query("PRAGMA table_info(admins)")->fetchAll(PDO::FETCH_ASSOC);
                echo "   Struktura tabeli 'admins':\n";
                foreach ($cols as $c) {
                    echo "     - {$c['name']} ({$c['type']})\n";
                }
            }
        } else {
            echo "❌ Baza istnieje, ale jest pusta.\n";
        }
    } catch (Exception $e) {
        echo "❌ Błąd połączenia z SQLite: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n⚠️  Brak pliku bazy danych: $dbFile\n";
}

echo "\n✅ Analiza zakończona.\n";

