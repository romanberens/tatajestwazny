<?php
session_start();
$_SESSION['csrf'] = 'testtoken';

echo "🧾 Symulacja danych z formularza strony\n";
$_POST = [
    'csrf' => 'testtoken',
    'action' => 'save_page',
    'slug' => 'nowa-strona',
    'title' => 'Nowa strona testowa',
    'body_html' => '<p>Nowa treść testowa</p>'
];

file_put_contents(__DIR__ . '/post_data_dump.log', print_r($_POST, true));

echo "✅ Zapisano dane do post_data_dump.log\n";
