<?php
require_once __DIR__.'/../../includes/auth.php';
auth();

$title = "Panel administracyjny";
ob_start();
?>
<h1 class="text-xl font-semibold mb-4">Panel administracyjny</h1>
<ul class="space-y-2">
  <li><a href="/admin/pages/index.php" class="text-blue-600 hover:underline">📝 Podstrony</a></li>
  <li><a href="/admin/menu/index.php" class="text-blue-600 hover:underline">📋 Menu</a></li>
  <li><a href="/admin/blog/index.php" class="text-blue-600 hover:underline">✍️ Blog</a></li>
  <li><a href="/admin/reset_password.php" class="text-blue-600 hover:underline">🔐 Zmień hasło</a></li>
  <li><a href="/admin/auth.php?action=logout" class="text-red-600 hover:underline">🚪 Wyloguj się</a></li>
</ul>
<?php
$content = ob_get_clean();
include_once __DIR__.'/../../templates/admin_layout.php';
