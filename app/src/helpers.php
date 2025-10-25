<?php

declare(strict_types=1);

function safe_html(?string $value, array $allowedTags = ['p', 'strong', 'em', 'ul', 'ol', 'li', 'a', 'br', 'span']): string
{
    if ($value === null) {
        return '';
    }
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $allowed = '<' . implode('><', $allowedTags) . '>';
    $stripped = strip_tags($value, $allowed);
    $stripped = preg_replace('/on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $stripped ?? '');
    $stripped = preg_replace('/javascript:/i', '', $stripped ?? '');

    return $stripped ?? '';
}

function html_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('CSRF validation requires POST request');
    }
    $token = $_POST['csrf'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!is_string($token) || !is_string($sessionToken) || !hash_equals($sessionToken, $token)) {
        logLine('WARNING', 'CSRF token mismatch');
        throw new RuntimeException('Invalid CSRF token');
    }
}

function get_post_value(string $key, bool $allowHtml = false): string
{
    $raw = $_POST[$key] ?? '';
    if (!is_string($raw)) {
        if (is_numeric($raw)) {
            $raw = (string)$raw;
        } else {
            return '';
        }
    }
    $raw = trim($raw);
    if ($allowHtml) {
        return safe_html($raw);
    }
    return $raw;
}

