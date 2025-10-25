<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

const DEFAULT_ADMIN_USER = 'admin';
const DEFAULT_ADMIN_PASSWORD = 'admin123';

function get_password_hash(): string
{
    $envHash = getenv('AUTH_PASSWORD_HASH');
    if (is_string($envHash) && $envHash !== '') {
        return $envHash;
    }
    return password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT);
}

function authenticate(string $username, string $password): bool
{
    $username = trim($username);
    if ($username === '') {
        return false;
    }
    $hash = get_password_hash();
    if (!hash_equals(DEFAULT_ADMIN_USER, $username)) {
        logLine('WARNING', sprintf('Failed login for user "%s"', $username));
        return false;
    }
    $valid = password_verify($password, $hash);
    if ($valid) {
        $_SESSION['user'] = $username;
        logLine('INFO', sprintf('User "%s" authenticated', $username));
    } else {
        logLine('WARNING', sprintf('Password verification failed for "%s"', $username));
    }
    return $valid;
}

function require_auth(): void
{
    if (!isset($_SESSION['user']) || $_SESSION['user'] !== DEFAULT_ADMIN_USER) {
        throw new RuntimeException('Authentication required');
    }
}

function logout(): void
{
    unset($_SESSION['user']);
    logLine('INFO', 'User logged out');
}
