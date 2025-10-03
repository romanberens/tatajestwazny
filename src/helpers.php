<?php
function render(string $template, array $vars = [], string $layout='layout.php'): void {
  extract($vars, EXTR_SKIP);
  ob_start(); include __DIR__ . '/../templates/' . $template; $content = ob_get_clean();
  $title = $vars['title'] ?? 'Tata Jest Ważny';
  include __DIR__ . '/../templates/' . $layout;
}
function render_admin(string $template, array $vars=[]): void {
  render($template, $vars, 'admin_layout.php');
}
function redirect(string $url): never { header("Location: $url"); exit; }
function require_post_csrf(): void {
  if ($_SERVER['REQUEST_METHOD']!=='POST' || empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
    http_response_code(400); echo "Bad CSRF"; exit;
  }
}
