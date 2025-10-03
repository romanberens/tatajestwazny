<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$db = new PDO('sqlite:../../../db/tjw.sqlite');
$db->prepare("DELETE FROM menu_items WHERE id = ?")->execute([$id]);
header("Location: index.php");
exit;
