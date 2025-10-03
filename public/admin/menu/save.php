<?php require 'csrf.php'; auth();
$db = new PDO('sqlite:../../../db/tjw.sqlite');
csrf_check($_POST['csrf'] ?? '');

$label = trim($_POST['label'] ?? '');
$type = $_POST['type'] ?? '';
$target = $type === 'internal' ? ($_POST['target_internal'] ?? '') : ($_POST['target_external'] ?? '');
$visible = isset($_POST['visible']) ? 1 : 0;

if (!$label || !$type || !$target) die("❌ Brak danych");

if ($type === 'external' && !preg_match('#^https?://#', $target))
  die("❌ Błędny adres URL");

$maxPos = $db->query("SELECT COALESCE(MAX(position),0) FROM menu_items")->fetchColumn();
$stmt = $db->prepare("INSERT INTO menu_items(label, type, target, visible, position) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$label, $type, $target, $visible, $maxPos + 1]);

header("Location: index.php");
exit;
