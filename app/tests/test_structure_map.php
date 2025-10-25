<?php
/**
 * 📊 test_structure_map.php
 * Analiza statyczna logiki CMS: funkcje, klasy, metody, zależności.
 * Tworzy mapę systemu i zapisuje do logs/cms_structure_map_<timestamp>.log
 */

date_default_timezone_set('Europe/Warsaw');

$baseDir = '/var/www/html/app';
$logDir  = "$baseDir/logs/diagnostics";
@mkdir($logDir, 0775, true);
$logFile = $logDir . '/cms_structure_map_' . date('Ymd_His') . '.log';

function logLine(string $msg): void {
    global $logFile;
    file_put_contents($logFile, '['.date('H:i:s')."] $msg\n", FILE_APPEND);
}

logLine("=== 📋 CMS STRUCTURE MAP TEST START ===");
logLine("Base dir: $baseDir");

// 🔍 1️⃣ Znajdź wszystkie pliki PHP w projekcie
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$phpFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
        $phpFiles[] = $file->getPathname();
    }
}
logLine("Znaleziono " . count($phpFiles) . " plików PHP");

// 🔍 2️⃣ Analiza każdej funkcji i klasy
foreach ($phpFiles as $file) {
    $code = file_get_contents($file);
    if ($code === false) continue;

    // Wyciągnięcie nazw funkcji
    if (preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)/', $code, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $fnName = $m[1];
            $params = trim($m[2]);
            logLine("FUNC [$fnName] ($params) in " . str_replace($baseDir, '', $file));
        }
    }

    // Wyciągnięcie nazw klas
    if (preg_match_all('/class\s+([A-Za-z0-9_]+)/', $code, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $class = $m[1];
            logLine("CLASS [$class] in " . str_replace($baseDir, '', $file));

            try {
                include_once $file;
                if (class_exists($class, false)) {
                    $ref = new ReflectionClass($class);
                    foreach ($ref->getMethods() as $method) {
                        if ($method->isConstructor()) continue;
                        $paramList = [];
                        foreach ($method->getParameters() as $p) {
                            $type = $p->hasType() ? (string)$p->getType() : 'mixed';
                            $paramList[] = '$' . $p->getName() . ':' . $type;
                        }
                        $retType = $method->hasReturnType() ? (string)$method->getReturnType() : 'void';
                        logLine(" ↳ METHOD {$method->getName()}(" . implode(', ', $paramList) . ") : $retType");
                    }
                }
            } catch (Throwable $e) {
                logLine(" ⚠️ Reflection error for $class: " . $e->getMessage());
            }
        }
    }
}

// 🔍 3️⃣ Analiza wywołań między plikami (include, require)
foreach ($phpFiles as $file) {
    $code = file_get_contents($file);
    if ($code === false) continue;

    if (preg_match_all('/require(_once)?\s*\(?[\'"]([^\'"]+)/', $code, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $req = $m[2];
            logLine("INCLUDE in " . str_replace($baseDir, '', $file) . " → $req");
        }
    }
}

// 🔍 4️⃣ Szukanie potencjalnych punktów wejścia POST
foreach ($phpFiles as $file) {
    $code = file_get_contents($file);
    if ($code === false) continue;

    if (preg_match('/\$_POST/', $code)) {
        logLine("POST reference in " . str_replace($baseDir, '', $file));
    }
    if (preg_match('/require_post_csrf/', $code)) {
        logLine("CSRF check in " . str_replace($baseDir, '', $file));
    }
}

logLine("=== ✅ CMS STRUCTURE MAP TEST END ===");
echo "✅ Raport zapisano do: $logFile\n";

