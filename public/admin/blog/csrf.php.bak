<?php
session_start();

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_check($token) {
  if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
    die("❌ Niepoprawny token CSRF.");
  }
}

function auth() {
  $USER = 'admin';
  $PASS = 'tajnehaslo'; // 👈 ZMIEŃ TO PRZY WDROŻENIU

  if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ||
      $_SERVER['PHP_AUTH_USER'] !== $USER ||
      $_SERVER['PHP_AUTH_PW'] !== $PASS) {
    header('WWW-Authenticate: Basic realm="Admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo '❌ Dostęp zabroniony';
    exit;
  }
}
