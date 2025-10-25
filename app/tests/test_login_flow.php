<?php
require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../includes/auth.php';

$_SESSION = [];
$token = generate_csrf_token();
if (!is_string($token) || strlen($token) < 32) {
    throw new RuntimeException('CSRF token not generated correctly');
}

if (!authenticate('admin', DEFAULT_ADMIN_PASSWORD)) {
    throw new RuntimeException('Admin credentials should authenticate');
}

if (!isset($_SESSION['user']) || $_SESSION['user'] !== DEFAULT_ADMIN_USER) {
    throw new RuntimeException('Session should contain authenticated user');
}

echo "test_login_flow: OK\n";
