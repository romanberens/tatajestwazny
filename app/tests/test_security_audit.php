<?php
require_once __DIR__ . '/../src/AdminHandlers.php';

@unlink(APP_DB_FILE);
Db::getConnection();

$_SESSION = [];
$token = generate_csrf_token();

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'csrf' => 'invalid',
    'slug' => 'csrf-test',
    'title' => 'CSRF Test',
    'body_html' => '<p>Test</p>',
];

try {
    handlePagesPost('create');
    throw new RuntimeException('CSRF validation should fail');
} catch (RuntimeException $e) {
    // expected
}

$_POST['csrf'] = $token;
$response = handlePagesPost('create');
if (!$response['success']) {
    throw new RuntimeException('Valid CSRF request should succeed');
}

echo "test_security_audit: OK\n";
