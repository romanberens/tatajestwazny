<?php
/**
 * 🧪 test_runtime_trace.php (v3)
 * Dynamiczny tracer logiki CMS — poprawiona ścieżka do helpers.php
 */

declare(strict_types=1);
date_default_timezone_set('Europe/Warsaw');

$baseDir = '/var/www/html/app';
$logDir  = "$baseDir/logs/diagnostics";
@mkdir($logDir, 0775, true);
$logFile = $logDir . '/cms_runtime_trace_' . date('Ymd_His') . '.log';

function logLine(string $msg): void {
    global $logFile;
    file_put_contents($logFile, '['.date('H:i:s')."] $msg\n", FILE_APPEND);
}

logLine("=== 🚦 CMS RUNTIME TRACE START ===");

// 1️⃣ Załaduj środowisko CMS
require_once "$baseDir/src/Db.php";
require_once "$baseDir/src/Repositories/PagesRepository.php";
require_once "$baseDir/src/Repositories/PostsRepository.php";
require_once "$baseDir/src/Repositories/MenuRepository.php";
require_once "$baseDir/src/AdminHandlers.php";
require_once "$baseDir/src/helpers.php"; // ✅ poprawiona ścieżka

$db = new Db();
$pdo = $db->pdo;

// 2️⃣ Przygotuj dane testowe symulujące formularz POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SESSION = ['csrf' => '12345'];
$_POST = [
    'csrf' => '12345',
    'action' => 'save_page',
    'slug' => 'strona-testowa',
    'title' => 'Strona testowa z trace',
    'body_html' => '<p>Testowa treść</p>',
];

// 3️⃣ Hook: przechwyć i zmierz dane wejściowe i wyjściowe
function trace_call(string $name, callable $fn) {
    logLine("→ CALL: $name()");
    $start = microtime(true);
    try {
        $result = $fn();
        $time = round((microtime(true) - $start) * 1000, 2);
        logLine("← RETURN: $name() OK ({$time} ms)");
        return $result;
    } catch (Throwable $e) {
        $time = round((microtime(true) - $start) * 1000, 2);
        logLine("💥 ERROR in $name(): ".$e->getMessage()." @".$e->getFile().":".$e->getLine()." ({$time} ms)");
        return null;
    }
}

// 4️⃣ Uruchom symulowany przepływ: handlePagesPost → save()
logLine("🧩 INIT PagesRepository + test handlePagesPost()");
$pagesRepo = new PagesRepository($pdo);

trace_call('handlePagesPost', function() use ($pagesRepo) {
    if (function_exists('handlePagesPost')) {
        handlePagesPost('save_page', $pagesRepo);
    } else {
        throw new Exception('Brak funkcji handlePagesPost()');
    }
});

// 5️⃣ Zweryfikuj, czy dane faktycznie trafiły do bazy
logLine("🧩 Weryfikacja wpisu w bazie");
$stmt = $pdo->query("SELECT id, slug, title, length(body_html) AS len FROM pages ORDER BY id DESC LIMIT 1");
$row = $stmt->fetch();
if ($row) {
    logLine("✅ Last inserted: ID={$row['id']}, slug={$row['slug']}, title={$row['title']}, body_len={$row['len']}");
} else {
    logLine("⚠️ Brak wpisów w tabeli pages (nie zapisano nic).");
}

logLine("=== ✅ CMS RUNTIME TRACE END ===");
echo "✅ Raport zapisano do: $logFile\n";
