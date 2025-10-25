<?php
/**
 * 🧩 test_db_integrity.php
 * Sprawdza spójność bazy SQLite tjw.sqlite – klucze obce, struktura, duplikaty.
 */
declare(strict_types=1);

$dbFile = '/var/www/html/app/db/tjw.sqlite';
if (!file_exists($dbFile)) {
    echo "❌ Brak pliku bazy danych: $dbFile\n";
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "🧠 Test integralności bazy danych tjw.sqlite\n";
    echo "------------------------------------------\n";

    // 1️⃣ Sprawdź integralność PRAGMA integrity_check
    $check = $pdo->query('PRAGMA integrity_check;')->fetchColumn();
    echo "PRAGMA integrity_check: $check\n";
    if ($check !== 'ok') {
        echo "❌ Wykryto błędy integralności!\n";
    }

    // 2️⃣ Sprawdź obecność kluczy obcych
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $fks = $pdo->query("PRAGMA foreign_key_list('$table');")->fetchAll();
        if ($fks) {
            echo "✅ $table — posiada klucze obce (" . count($fks) . ")\n";
        } else {
            echo "⚠️ $table — brak kluczy obcych\n";
        }
    }

    // 3️⃣ Sprawdź duplikaty w tabelach głównych
    $keyTables = ['pages' => 'slug', 'posts' => 'slug', 'menu_items' => 'id'];
    foreach ($keyTables as $table => $col) {
        $dup = $pdo->query("SELECT $col, COUNT(*) c FROM $table GROUP BY $col HAVING c > 1;")->fetchAll();
        if ($dup) {
            echo "⚠️ $table — duplikaty w kolumnie $col: " . count($dup) . "\n";
        } else {
            echo "✅ $table — brak duplikatów\n";
        }
    }

    echo "✅ Test zakończony.\n";
} catch (Throwable $e) {
    echo "❌ Błąd: " . $e->getMessage() . "\n";
}
