<?php
require_once __DIR__ . '/../src/bootstrap.php';

logLine('INFO', 'Runtime trace test entry');

$log = file_get_contents(APP_RUNTIME_LOG);
if ($log === false || strpos($log, 'Runtime trace test entry') === false) {
    throw new RuntimeException('Runtime log entry not found');
}

echo "test_runtime_trace: OK\n";
