<?php

declare(strict_types=1);

/**
 * Starts a secure PHP session with hardened cookie options.
 */
function ensure_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');

    $params = session_get_cookie_params();

    $secure = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    // 🧩 Wymuszone, bezpieczne parametry cookie
    $cookieParams = [
        'lifetime' => $params['lifetime'] ?? 0,
        'path' => $params['path'] ?? '/',
        'domain' => $params['domain'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => $params['samesite'] ?? 'Lax',
    ];

    // ✅ Poprawna forma dla PHP 8.3 (bez błędu „path cannot contain …”)
    session_set_cookie_params([
        'lifetime' => $cookieParams['lifetime'],
        'path'     => $cookieParams['path'],
        'domain'   => $cookieParams['domain'],
        'secure'   => $cookieParams['secure'],
        'httponly' => $cookieParams['httponly'],
        'samesite' => $cookieParams['samesite'],
    ]);

    if (!headers_sent()) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', $cookieParams['samesite']);
        ini_set('session.cookie_secure', $secure ? '1' : '0');
    }

    session_start();
}

/**
 * Forces regeneration of session ID to prevent fixation attacks.
 */
function force_regenerate_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ensure_secure_session();
    }

    session_regenerate_id(true);
}

/**
 * Logs out current user and securely destroys the session.
 */
function logout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ensure_secure_session();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        // ✅ Wersja w pełni zgodna z PHP 7.3–8.3
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $params['path'] ?? '/',
            'domain'   => $params['domain'] ?? '',
            'secure'   => $params['secure'] ?? false,
            'httponly' => $params['httponly'] ?? true,
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}

/**
 * Enforces authentication for admin panel access.
 */
function auth(): void
{
    ensure_secure_session();

    if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: /admin/auth.php');
        exit;
    }
}

/**
 * Returns the currently configured admin password hash from .env or environment.
 */
function getCurrentPasswordHash(): ?string
{
    // Najpierw spróbuj z $_ENV (po load_env.php)
    if (!empty($_ENV['AUTH_PASSWORD_HASH'])) {
        return $_ENV['AUTH_PASSWORD_HASH'];
    }

    // Awaryjnie — odczytaj bezpośrednio z pliku .env
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), 'AUTH_PASSWORD_HASH=') === 0) {
                [$key, $value] = explode('=', $line, 2);
                return trim($value, "\"' ");
            }
        }
    }

    return null;
}
