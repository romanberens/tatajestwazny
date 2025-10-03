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

if ($path === '/') {
    render('home.php', [
        'title' => 'Tata Jest Ważny',
        'navItems' => $navItems,
        'misja' => $blocksRepository->byRegion('misja'),
        'jak_pomagam' => $blocksRepository->byRegion('jak_pomagam'),
        'szybka_pomoc' => $blocksRepository->byRegion('szybka_pomoc'),
        'dla_kogo' => $blocksRepository->byRegion('dla_kogo'),
    ]);
    return;
}

if ($path === '/blog') {
    render('blog_list.php', [
        'title' => 'Blog',
        'navItems' => $navItems,
        'posts' => $postsRepository->published(),
    ]);
    return;
}

if (preg_match('#^/blog/([a-z0-9\-]+)$#', $path, $matches)) {
    $post = $postsRepository->findBySlug($matches[1]);
    if ($post) {
        render('blog_post.php', [
            'title' => $post['title'],
            'navItems' => $navItems,
            'post' => $post,
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
            'page' => $page,
        ]);
        return;
    }
}

http_response_code(404);
render('404.php', [
    'title' => 'Strona nie istnieje',
    'navItems' => $navItems,
]);
