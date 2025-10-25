<?php

declare(strict_types=1);

function logLine(string $level, string $message): void
{
    $level = strtoupper(trim($level));
    $timestamp = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM);
    $line = sprintf('[%s] %s %s%s', $timestamp, $level, trim($message), PHP_EOL);
    $logFile = defined('APP_RUNTIME_LOG') ? APP_RUNTIME_LOG : (__DIR__ . '/../logs/diagnostics/runtime.log');
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fp = fopen($logFile, 'ab');
    if ($fp === false) {
        throw new RuntimeException('Unable to open runtime log for writing');
    }
    fwrite($fp, $line);
    fclose($fp);
}
