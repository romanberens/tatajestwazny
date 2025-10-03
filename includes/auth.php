<?php
session_start();

function auth() {
  if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /admin/auth.php');
    exit;
  }
}
