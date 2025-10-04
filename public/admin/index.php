<?php
require __DIR__ . '/../../src/bootstrap.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/load_env.php';

ensure_secure_session();
auth();

$db = new Db();
$pdo = $db->pdo;
$blocksRepository = new Blocks($pdo);
$pagesRepository = new PagesRepository($pdo);
$menuRepository = new MenuRepository($pdo);
$postsRepository = new PostsRepository($pdo);

$section = $_GET['section'] ?? 'dashboard';
$action = $_POST['action'] ?? $_GET['action'] ?? null;

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$formError = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();

    switch ($section) {
        case 'blocks':
            handleBlocksPost($action, $blocksRepository);
            break;
        case 'pages':
            handlePagesPost($action, $pagesRepository);
            break;
        case 'menu':
            handleMenuPost($action, $menuRepository);
            break;
        case 'blog':
            handleBlogPost($action, $postsRepository);
            break;
        case 'settings':
            handleSettingsPost($action);
            break;
    }
}

$nav = [
    ['id' => 'dashboard', 'label' => 'Przegląd', 'href' => '/admin/?section=dashboard', 'icon' => '📊'],
    ['id' => 'blocks', 'label' => 'Bloki treści', 'href' => '/admin/?section=blocks', 'icon' => '🧱'],
    ['id' => 'pages', 'label' => 'Podstrony', 'href' => '/admin/?section=pages', 'icon' => '📝'],
    ['id' => 'menu', 'label' => 'Menu', 'href' => '/admin/?section=menu', 'icon' => '📋'],
    ['id' => 'blog', 'label' => 'Blog', 'href' => '/admin/?section=blog', 'icon' => '✍️'],
    ['id' => 'settings', 'label' => 'Ustawienia', 'href' => '/admin/?section=settings', 'icon' => '⚙️'],
];

$template = 'admin_dashboard.php';
$vars = [
    'title' => 'Panel administracyjny',
    'nav' => $nav,
    'activeSection' => $section,
    'flash' => $flash,
    'formError' => $formError,
];

switch ($section) {
    case 'dashboard':
        $template = 'admin_dashboard.php';
        $vars += [
            'title' => 'Przegląd',
            'stats' => gatherStats($blocksRepository, $pagesRepository, $menuRepository, $postsRepository),
            'recentPosts' => array_slice($postsRepository->all(), 0, 3),
        ];
        break;

    case 'blocks':
        $regions = ['misja', 'jak_pomagam', 'szybka_pomoc', 'dla_kogo', 'inne'];
        if ($action === 'edit' && isset($_GET['id'])) {
            $block = $blocksRepository->find((int) $_GET['id']);
            if (!$block) {
                $_SESSION['flash'] = 'Nie znaleziono bloku.';
                redirect('/admin/?section=blocks');
            }
            $template = 'admin_block_form.php';
            $vars += [
                'title' => 'Edycja bloku',
                'block' => array_merge($block, $formData),
                'regions' => $regions,
            ];
        } elseif ($action === 'new') {
            $template = 'admin_block_form.php';
            $vars += [
                'title' => 'Nowy blok',
                'block' => $formData,
                'regions' => $regions,
            ];
        } else {
            $template = 'admin_blocks_list.php';
            $vars += [
                'title' => 'Bloki treści',
                'items' => $blocksRepository->all(),
            ];
        }
        break;

    case 'pages':
        if ($action === 'edit' && isset($_GET['id'])) {
            $page = $pagesRepository->find((int) $_GET['id']);
            if (!$page) {
                $_SESSION['flash'] = 'Nie znaleziono podstrony.';
                redirect('/admin/?section=pages');
            }
            $template = 'admin_pages_form.php';
            $vars += [
                'title' => 'Edycja podstrony',
                'page' => array_merge($page, $formData),
            ];
        } elseif ($action === 'new') {
            $template = 'admin_pages_form.php';
            $vars += [
                'title' => 'Nowa podstrona',
                'page' => $formData,
            ];
        } else {
            $template = 'admin_pages_list.php';
            $vars += [
                'title' => 'Podstrony',
                'pages' => $pagesRepository->all(),
            ];
        }
        break;

    case 'menu':
        if ($action === 'edit' && isset($_GET['id'])) {
            $item = $menuRepository->find((int) $_GET['id']);
            if (!$item) {
                $_SESSION['flash'] = 'Nie znaleziono elementu menu.';
                redirect('/admin/?section=menu');
            }
            $template = 'admin_menu_form.php';
            $vars += [
                'title' => 'Edycja elementu menu',
                'item' => array_merge($item, $formData),
            ];
        } elseif ($action === 'new') {
            $template = 'admin_menu_form.php';
            $vars += [
                'title' => 'Nowe menu',
                'item' => $formData ?: ['type' => 'internal', 'visible' => 1],
            ];
        } else {
            $template = 'admin_menu_list.php';
            $vars += [
                'title' => 'Menu',
                'items' => $menuRepository->all(),
            ];
        }
        break;

    case 'blog':
        if ($action === 'edit' && isset($_GET['id'])) {
            $post = $postsRepository->find((int) $_GET['id']);
            if (!$post) {
                $_SESSION['flash'] = 'Nie znaleziono wpisu.';
                redirect('/admin/?section=blog');
            }
            $template = 'admin_posts_form.php';
            $vars += [
                'title' => 'Edycja wpisu',
                'post' => array_merge($post, $formData),
            ];
        } elseif ($action === 'new') {
            $template = 'admin_posts_form.php';
            $vars += [
                'title' => 'Nowy wpis',
                'post' => $formData,
            ];
        } else {
            $template = 'admin_posts_list.php';
            $vars += [
                'title' => 'Blog',
                'posts' => $postsRepository->all(),
            ];
        }
        break;

    case 'settings':
        $template = 'admin_settings.php';
        $vars += [
            'title' => 'Ustawienia',
            'currentHash' => getCurrentPasswordHash(),
        ];
        break;

    default:
        $template = 'admin_dashboard.php';
        $vars += [
            'title' => 'Przegląd',
            'stats' => gatherStats($blocksRepository, $pagesRepository, $menuRepository, $postsRepository),
            'recentPosts' => array_slice($postsRepository->all(), 0, 3),
        ];
}

render_admin($template, $vars);
