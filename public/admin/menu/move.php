<?php require 'csrf.php'; auth();
$id = (int)($_GET['id'] ?? 0);
$dir = $_GET['dir'] ?? 'down';
$db = new PDO('sqlite:../../../db/tjw.sqlite');

$item = $db->query("SELECT id, position FROM menu_items WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
if (!$item) exit;

$cmp = $dir === 'up' ? '<' : '>';
$order = $dir === 'up' ? 'DESC' : 'ASC';

$stmt = $db->prepare("SELECT id, position FROM menu_items WHERE position $cmp ? ORDER BY position $order LIMIT 1");
$stmt->execute([$item['position']]);
$swap = $stmt->fetch(PDO::FETCH_ASSOC);

if ($swap) {
  $db->prepare("UPDATE menu_items SET position = ? WHERE id = ?")->execute([$item['position'], $swap['id']]);
  $db->prepare("UPDATE menu_items SET position = ? WHERE id = ?")->execute([$swap['position'], $item['id']]);
}
header("Location: index.php");
exit;
