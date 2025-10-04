<?php
require_once __DIR__ . '/../includes/auth.php';
ensure_secure_session();

require __DIR__ . '/helpers.php';
require __DIR__ . '/Db.php';
require __DIR__ . '/Blocks.php';
require __DIR__ . '/Repositories/PagesRepository.php';
require __DIR__ . '/Repositories/MenuRepository.php';
require __DIR__ . '/Repositories/PostsRepository.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
