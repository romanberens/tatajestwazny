<?php
require_once __DIR__ . '/../includes/auth.php';
ensure_secure_session();

require __DIR__ . '/helpers.php';
require __DIR__ . '/AdminHandlers.php';   // ✅ dodane – obsługa handleBlogPost, handlePagesPost itd.
require __DIR__ . '/Db.php';
require __DIR__ . '/Blocks.php';
require __DIR__ . '/Repositories/PagesRepository.php';
require __DIR__ . '/Repositories/MenuRepository.php';
require __DIR__ . '/Repositories/PostsRepository.php';

// ✅ Wczytanie funkcji gatherStats() (bez błędu jeśli plik nie istnieje)
$statsFile = __DIR__ . '/Stats.php';
if (file_exists($statsFile)) {
    require_once $statsFile;
} else {
    error_log("[bootstrap] Warning: Stats.php not found – gatherStats() will be undefined.");
}

// 🧩 Ustawienie CSRF, jeśli jeszcze nie istnieje
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
