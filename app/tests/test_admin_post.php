<?php
require_once __DIR__ . '/../src/AdminHandlers.php';

@unlink(APP_DB_FILE);
Db::getConnection();

$_SESSION = [];
$token = generate_csrf_token();

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'csrf' => $token,
    'slug' => 'test-page',
    'title' => 'Test Page',
    'body_html' => '<p>Hello <script>alert(1)</script><strong>World</strong></p>',
    'action' => 'create',
];

$response = handlePagesPost('create');
if (!$response['success']) {
    throw new RuntimeException('Failed to create page: ' . ($response['error'] ?? 'unknown error'));
}

$pagesRepository = new PagesRepository();
$page = $pagesRepository->findBySlug('test-page');
if ($page === null) {
    throw new RuntimeException('Page was not persisted');
}

if (strpos($page['body_html'], 'script') !== false) {
    throw new RuntimeException('HTML sanitization failed');
}

$_POST = [
    'csrf' => $token,
    'id' => $response['id'],
];

$responseDelete = handlePagesPost('delete');
if (!$responseDelete['success']) {
    throw new RuntimeException('Failed to delete page');
}

$pageAfterDelete = $pagesRepository->findBySlug('test-page');
if ($pageAfterDelete !== null) {
    throw new RuntimeException('Page was not deleted');
}

echo "test_admin_post: OK\n";
