<?php
require __DIR__ . '/../../src/bootstrap.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/load_env.php';

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

function gatherStats(Blocks $blocks, PagesRepository $pages, MenuRepository $menu, PostsRepository $posts): array
{
    return [
        'blocks' => count($blocks->all()),
        'pages' => count($pages->all()),
        'menu' => count($menu->all()),
        'posts' => count($posts->all()),
    ];
}

function handleBlocksPost(?string $action, Blocks $blocks): void
{
    switch ($action) {
        case 'save_block':
            try {
                $blocks->save($_POST);
                $_SESSION['flash'] = 'Blok został zapisany.';
                redirect('/admin/?section=blocks');
            } catch (InvalidArgumentException $e) {
                $_SESSION['form_error'] = $e->getMessage();
                $_SESSION['form_data'] = sanitizeFormData($_POST);
                $target = !empty($_POST['id']) ? '/admin/?section=blocks&action=edit&id=' . (int) $_POST['id'] : '/admin/?section=blocks&action=new';
                redirect($target);
            }
            break;
        case 'delete_block':
            $blocks->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Blok został usunięty.';
            redirect('/admin/?section=blocks');
            break;
        case 'move_block':
            $blocks->move((int) ($_POST['id'] ?? 0), (int) ($_POST['delta'] ?? 0));
            $_SESSION['flash'] = 'Zmieniono kolejność bloku.';
            redirect('/admin/?section=blocks');
            break;
    }
}

function handlePagesPost(?string $action, PagesRepository $pages): void
{
    switch ($action) {
        case 'save_page':
            try {
                $pages->save($_POST);
                $_SESSION['flash'] = 'Podstrona została zapisana.';
                redirect('/admin/?section=pages');
            } catch (InvalidArgumentException $e) {
                $_SESSION['form_error'] = $e->getMessage();
                $_SESSION['form_data'] = sanitizeFormData($_POST);
                $target = !empty($_POST['id']) ? '/admin/?section=pages&action=edit&id=' . (int) $_POST['id'] : '/admin/?section=pages&action=new';
                redirect($target);
            }
            break;
        case 'delete_page':
            $pages->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Podstrona została usunięta.';
            redirect('/admin/?section=pages');
            break;
    }
}

function handleMenuPost(?string $action, MenuRepository $menu): void
{
    switch ($action) {
        case 'save_menu':
            try {
                $menu->save($_POST);
                $_SESSION['flash'] = 'Element menu został zapisany.';
                redirect('/admin/?section=menu');
            } catch (InvalidArgumentException $e) {
                $_SESSION['form_error'] = $e->getMessage();
                $_SESSION['form_data'] = sanitizeFormData($_POST);
                $target = !empty($_POST['id']) ? '/admin/?section=menu&action=edit&id=' . (int) $_POST['id'] : '/admin/?section=menu&action=new';
                redirect($target);
            }
            break;
        case 'delete_menu':
            $menu->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Element menu został usunięty.';
            redirect('/admin/?section=menu');
            break;
    }
}

function handleBlogPost(?string $action, PostsRepository $posts): void
{
    switch ($action) {
        case 'save_post':
            try {
                $posts->save($_POST);
                $_SESSION['flash'] = 'Wpis został zapisany.';
                redirect('/admin/?section=blog');
            } catch (InvalidArgumentException $e) {
                $_SESSION['form_error'] = $e->getMessage();
                $_SESSION['form_data'] = sanitizeFormData($_POST);
                $target = !empty($_POST['id']) ? '/admin/?section=blog&action=edit&id=' . (int) $_POST['id'] : '/admin/?section=blog&action=new';
                redirect($target);
            }
            break;
        case 'delete_post':
            $posts->delete((int) ($_POST['id'] ?? 0));
            $_SESSION['flash'] = 'Wpis został usunięty.';
            redirect('/admin/?section=blog');
            break;
    }
}

function handleSettingsPost(?string $action): void
{
    if ($action !== 'update_password') {
        return;
    }

    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    $currentHash = getCurrentPasswordHash();
    if (!$currentHash || !password_verify($old, $currentHash)) {
        $_SESSION['form_error'] = 'Błędne obecne hasło.';
        redirect('/admin/?section=settings');
    }

    if ($new !== $repeat) {
        $_SESSION['form_error'] = 'Nowe hasła muszą być identyczne.';
        redirect('/admin/?section=settings');
    }

    if (strlen($new) < 8) {
        $_SESSION['form_error'] = 'Hasło musi mieć co najmniej 8 znaków.';
        redirect('/admin/?section=settings');
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);
    if (!updatePasswordHash($newHash)) {
        $_SESSION['form_error'] = 'Nie udało się zapisać nowego hasła.';
        redirect('/admin/?section=settings');
    }

    $_SESSION['flash'] = 'Hasło administratora zostało zaktualizowane.';
    redirect('/admin/?section=settings');
}

function sanitizeFormData(array $data): array
{
    unset($data['csrf'], $data['action']);
    return $data;
}

function getCurrentPasswordHash(): ?string
{
    $envPath = __DIR__ . '/../../.env';
    if (!file_exists($envPath)) {
        return null;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with($line, 'AUTH_PASSWORD_HASH=')) {
            return trim(substr($line, strlen('AUTH_PASSWORD_HASH=')), "\"' ");
        }
    }

    return null;
}

function updatePasswordHash(string $hash): bool
{
    $envPath = __DIR__ . '/../../.env';
    $lines = file_exists($envPath) ? file($envPath) : [];
    $found = false;

    foreach ($lines as &$line) {
        if (str_starts_with($line, 'AUTH_PASSWORD_HASH=')) {
            $line = "AUTH_PASSWORD_HASH=\"$hash\"\n";
            $found = true;
        }
    }

    if (!$found) {
        $lines[] = "AUTH_PASSWORD_HASH=\"$hash\"\n";
    }

    return file_put_contents($envPath, implode('', $lines)) !== false;
}
