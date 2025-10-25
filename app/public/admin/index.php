<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_once __DIR__ . '/../../includes/auth.php';

$token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel administracyjny</title>
</head>
<body>
    <h1>Panel administracyjny</h1>

    <section>
        <h2>Nowa strona</h2>
        <form method="post" action="/admin/pages.php">
            <input type="hidden" name="csrf" value="<?= html_escape($token) ?>">
            <label>Slug <input type="text" name="slug" required></label><br>
            <label>Tytuł <input type="text" name="title" required></label><br>
            <label>Treść <textarea name="body_html" required></textarea></label><br>
            <button type="submit" name="action" value="create">Zapisz</button>
        </form>
    </section>

    <section>
        <h2>Nowy wpis blogowy</h2>
        <form method="post" action="/admin/posts.php">
            <input type="hidden" name="csrf" value="<?= html_escape($token) ?>">
            <label>Slug <input type="text" name="slug" required></label><br>
            <label>Tytuł <input type="text" name="title" required></label><br>
            <label>Data publikacji <input type="datetime-local" name="published_at"></label><br>
            <label>Treść <textarea name="body_html" required></textarea></label><br>
            <button type="submit" name="action" value="create">Zapisz</button>
        </form>
    </section>

    <section>
        <h2>Element menu</h2>
        <form method="post" action="/admin/menu.php">
            <input type="hidden" name="csrf" value="<?= html_escape($token) ?>">
            <label>Etykieta <input type="text" name="label" required></label><br>
            <label>URL <input type="url" name="url" required></label><br>
            <label>Pozycja <input type="number" name="position" required></label><br>
            <button type="submit" name="action" value="create">Zapisz</button>
        </form>
    </section>

    <section>
        <h2>Blok treści</h2>
        <form method="post" action="/admin/blocks.php">
            <input type="hidden" name="csrf" value="<?= html_escape($token) ?>">
            <label>Identyfikator <input type="text" name="identifier" required></label><br>
            <label>Treść <textarea name="body_html" required></textarea></label><br>
            <button type="submit" name="action" value="create">Zapisz</button>
        </form>
    </section>
</body>
</html>
