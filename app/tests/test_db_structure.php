<?php
require __DIR__ . '/../src/Db.php';

$db = new Db();
$pdo = $db->pdo;

echo "📦 Test struktury bazy tjw.sqlite\n";

$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

echo "Znalezione tabele:\n";
foreach ($tables as $t) {
    echo "  - $t\n";
}

$expected = ['pages', 'posts', 'menu_items'];
$missing = array_diff($expected, $tables);

if ($missing) {
    echo "❌ Brakuje tabel: " . implode(', ', $missing) . "\n";
} else {
    echo "✅ Wszystkie kluczowe tabele istnieją\n";
}

