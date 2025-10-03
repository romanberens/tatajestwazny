<?php
require __DIR__.'/includes/layout.php';
$db = new PDO('sqlite:../db/tjw.sqlite');

$uri = $_SERVER['REQUEST_URI'];

// 📄 /blog/<slug>
if (preg_match('#^/blog/([a-z0-9\-]+)$#', $uri, $m)) {
  $slug = $m[1];
  $stmt = $db->prepare("SELECT title, body_html FROM posts WHERE slug = ? AND published_at IS NOT NULL");
  $stmt->execute([$slug]);
  $post = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$post) {
    http_response_code(404);
    layout("404", "<h1>Nie znaleziono wpisu</h1>");
    exit;
  }
  layout($post['title'], $post['body_html']);
  exit;
}

// 🗂️ /blog (lista)
if ($uri === '/blog') {
  $posts = $db->query("SELECT slug, title, excerpt, published_at FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC")->fetchAll();
  $html = "<h1>📚 Blog</h1><ul>";
  foreach ($posts as $p) {
    $html .= "<li><a href='/blog/" . htmlspecialchars($p['slug']) . "'><strong>" . htmlspecialchars($p['title']) . "</strong></a>";
    if ($p['excerpt']) $html .= "<p>" . htmlspecialchars($p['excerpt']) . "</p>";
    $html .= "</li>";
  }
  $html .= "</ul>";
  layout("Blog", $html);
  exit;
}

// 🏠 domyślnie: strona główna (bloki)
$blocks = $db->query("SELECT title, body_html FROM content_blocks ORDER BY region, position")->fetchAll();
$body = '';
foreach ($blocks as $b) {
  $body .= "<h2>".htmlspecialchars($b['title'])."</h2>\n".$b['body_html']."\n";
}
layout("TataJestWazny", $body);
