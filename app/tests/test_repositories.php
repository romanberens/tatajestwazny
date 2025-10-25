<?php
require __DIR__ . '/../src/Db.php';
require __DIR__ . '/../src/Repositories/PagesRepository.php';
require __DIR__ . '/../src/Repositories/PostsRepository.php';
require __DIR__ . '/../src/Repositories/MenuRepository.php';

echo "🧪 Test repozytoriów\n";

$db = new Db();
$pdo = $db->pdo;

// PAGE TEST
$pages = new PagesRepository($pdo);
try {
    $id = $pages->save([
        'slug' => 'test-page',
        'title' => 'Strona testowa',
        'body_html' => '<p>To jest testowa treść strony.</p>'
    ]);
    echo "✅ PagesRepository::save() OK, ID=$id\n";
} catch (Throwable $e) {
    echo "❌ PagesRepository::save() ERROR: " . $e->getMessage() . "\n";
}

// BLOG TEST
$posts = new PostsRepository($pdo);
try {
    $id = $posts->save([
        'slug' => 'test-post',
        'title' => 'Wpis testowy',
        'excerpt' => 'To jest skrót wpisu testowego.',
        'body_html' => '<p>Treść wpisu testowego.</p>'
    ]);
    echo "✅ PostsRepository::save() OK, ID=$id\n";
} catch (Throwable $e) {
    echo "❌ PostsRepository::save() ERROR: " . $e->getMessage() . "\n";
}

// MENU TEST
$menu = new MenuRepository($pdo);
try {
    $id = $menu->save([
        'label' => 'Test',
        'type' => 'internal',
        'target' => 'test-page',
        'position' => 1,
        'visible' => 1
    ]);
    echo "✅ MenuRepository::save() OK, ID=$id\n";
} catch (Throwable $e) {
    echo "❌ MenuRepository::save() ERROR: " . $e->getMessage() . "\n";
}
