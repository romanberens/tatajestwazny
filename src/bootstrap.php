<?php
session_start();
require __DIR__ . '/helpers.php';
require __DIR__ . '/Db.php';
require __DIR__ . '/Blocks.php';
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
