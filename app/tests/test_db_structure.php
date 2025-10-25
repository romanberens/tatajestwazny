<?php
require_once __DIR__ . '/../src/bootstrap.php';

$pdo = Db::getConnection();

$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
$expected = ['pages', 'posts', 'menu_items', 'content_blocks'];
foreach ($expected as $table) {
    if (!in_array($table, $tables, true)) {
        throw new RuntimeException("Missing expected table: {$table}");
    }
}

echo "test_db_structure: OK\n";
