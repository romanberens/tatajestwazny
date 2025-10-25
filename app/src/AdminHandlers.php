<?php
/**
 * Minimalne funkcje obsługi POST w panelu admina.
 * Wersja rozszerzona — pełna obsługa zmiany hasła w Ustawieniach.
 */

if (!function_exists('handleBlocksPost')) {
    function handleBlocksPost($action, $blocksRepository = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (function_exists('require_post_csrf')) {
                require_post_csrf();
            }
            error_log("[admin] handleBlocksPost($action) called");
            // TODO: implementacja zapisu bloków
        }
    }
}

if (!function_exists('handlePagesPost')) {
    function handlePagesPost($action, $pagesRepository = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (function_exists('require_post_csrf')) {
                require_post_csrf();
            }
            error_log("[admin] handlePagesPost($action) called");
        }
    }
}

if (!function_exists('handleMenuPost')) {
    function handleMenuPost($action, $menuRepository = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (function_exists('require_post_csrf')) {
                require_post_csrf();
            }
            error_log("[admin] handleMenuPost($action) called");
        }
    }
}

if (!function_exists('handleBlogPost')) {
    function handleBlogPost($action, $postsRepository = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (function_exists('require_post_csrf')) {
                require_post_csrf();
            }
            error_log("[admin] handleBlogPost($action) called");
        }
    }
}

if (!function_exists('handleSettingsPost')) {
    /**
     * Obsługuje zmianę hasła administratora w sekcji "Ustawienia".
     */
    function handleSettingsPost($action)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (function_exists('require_post_csrf')) {
            require_post_csrf();
        }

        error_log("[admin] handleSettingsPost($action) called");

        if ($action !== 'update_password') {
            return;
        }

        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $repeat = $_POST['repeat_password'] ?? '';

        if ($new !== $repeat) {
            $_SESSION['form_error'] = 'Nowe hasła nie są identyczne.';
            redirect('/admin/?section=settings');
        }

        if (strlen($new) < 8) {
            $_SESSION['form_error'] = 'Hasło musi mieć co najmniej 8 znaków.';
            redirect('/admin/?section=settings');
        }

        if (!function_exists('getCurrentPasswordHash')) {
            error_log("[admin] Brak funkcji getCurrentPasswordHash()");
            $_SESSION['form_error'] = 'Błąd konfiguracji systemu.';
            redirect('/admin/?section=settings');
        }

        $currentHash = getCurrentPasswordHash();
        if (!$currentHash || !password_verify($old, $currentHash)) {
            $_SESSION['form_error'] = 'Nieprawidłowe obecne hasło.';
            redirect('/admin/?section=settings');
        }

        // ✅ Utwórz nowy hash i zaktualizuj .env
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $envPath = __DIR__ . '/../.env';
        $backupPath = $envPath . '.bak_' . date('Ymd_His');
        $updated = false;
        $newLines = [];

        if (file_exists($envPath)) {
            // kopia bezpieczeństwa
            copy($envPath, $backupPath);

            $lines = file($envPath, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), 'AUTH_PASSWORD_HASH=') === 0) {
                    $newLines[] = 'AUTH_PASSWORD_HASH="' . $newHash . '"';
                    $updated = true;
                } else {
                    $newLines[] = $line;
                }
            }
        }

        if (!$updated) {
            $newLines[] = 'AUTH_PASSWORD_HASH="' . $newHash . '"';
        }

        file_put_contents($envPath, implode(PHP_EOL, $newLines) . PHP_EOL);

        $_SESSION['flash'] = '✅ Hasło zostało pomyślnie zmienione.';
        error_log("[admin] Hasło administratora zaktualizowane " . date('Y-m-d H:i:s'));

        redirect('/admin/?section=settings');
    }
}
