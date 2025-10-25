<?php
require_once __DIR__ . '/../../src/AdminHandlers.php';

$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'POST';
$action = $_POST['action'] ?? 'create';
$response = handleBlogPost($action);
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
