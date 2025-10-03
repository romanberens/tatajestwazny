<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
exit;
