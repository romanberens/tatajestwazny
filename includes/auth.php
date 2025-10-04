<?php

declare(strict_types=1);

/**
 * Ensures that the PHP session is started with hardened cookie options.
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

    $cookieParams = [
        'lifetime' => $params['lifetime'] ?? 0,
        'path' => $params['path'] ?? '/',
        'domain' => $params['domain'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => $params['samesite'] ?? 'Lax',
    ];

    session_set_cookie_params($cookieParams);

    if (!headers_sent()) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', $cookieParams['samesite']);
        ini_set('session.cookie_secure', $secure ? '1' : '0');
    }

    session_start();
}

/**
 * Forces the regeneration of the session identifier.
 */
function force_regenerate_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ensure_secure_session();
    }

    session_regenerate_id(true);
}

/**
 * Logs the current user out and destroys the session securely.
 */
function logout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        ensure_secure_session();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, [
            'path' => $params['path'] ?? '/',
            'domain' => $params['domain'] ?? '',
            'secure' => $params['secure'] ?? false,
            'httponly' => $params['httponly'] ?? true,
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}

function auth(): void
{
    ensure_secure_session();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: /admin/auth.php');
        exit;
    }
}
