<?php
require __DIR__ . '/../src/bootstrap.php';

$db = new Db();
$pdo = $db->pdo;
$blocksRepository = new Blocks($pdo);
$pagesRepository = new PagesRepository($pdo);
$menuRepository = new MenuRepository($pdo);
$postsRepository = new PostsRepository($pdo);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$navItems = $menuRepository->visible();

$sanitizeRichItem = static function (?array $item): ?array {
    if ($item === null) {
        return null;
    }

    foreach ($item as $key => $value) {
        if (is_string($value) && substr($key, -5) === '_html') {
            $item[$key] = safe_html($value);
        }
    }

    return $item;
};

$sanitizeCollection = static function (?array $items) use ($sanitizeRichItem): array {
    if (!is_array($items)) {
        return [];
    }

    $sanitized = [];
    foreach ($items as $key => $item) {
        $sanitized[$key] = is_array($item) ? $sanitizeRichItem($item) : $item;
    }

    return $sanitized;
};

if ($path === '/') {
    render('home.php', [
        'title' => 'Tata Jest Ważny',
        'navItems' => $navItems,
        'misja' => $sanitizeCollection($blocksRepository->byRegion('misja')),
        'jak_pomagam' => $sanitizeCollection($blocksRepository->byRegion('jak_pomagam')),
        'szybka_pomoc' => $sanitizeCollection($blocksRepository->byRegion('szybka_pomoc')),
        'dla_kogo' => $sanitizeCollection($blocksRepository->byRegion('dla_kogo')),
    ]);
    return;
}

if ($path === '/blog') {
    render('blog_list.php', [
        'title' => 'Blog',
        'navItems' => $navItems,
        'posts' => $sanitizeCollection($postsRepository->published()),
    ]);
    return;
}

if (preg_match('#^/blog/([a-z0-9\-]+)$#', $path, $matches)) {
    $post = $postsRepository->findBySlug($matches[1]);
    if ($post) {
        render('blog_post.php', [
            'title' => $post['title'],
            'navItems' => $navItems,
            'post' => $sanitizeRichItem($post),
        ]);
        return;
    }

    http_response_code(404);
    render('404.php', [
        'title' => 'Nie znaleziono wpisu',
        'navItems' => $navItems,
    ]);
    return;
}

$slug = trim($path, '/');
if ($slug !== '') {
    $page = $pagesRepository->findBySlug($slug);
    if ($page) {
        render('page.php', [
            'title' => $page['title'],
            'navItems' => $navItems,
            'page' => $sanitizeRichItem($page),
        ]);
        return;
    }
}

http_response_code(404);
render('404.php', [
    'title' => 'Strona nie istnieje',
    'navItems' => $navItems,
]);
