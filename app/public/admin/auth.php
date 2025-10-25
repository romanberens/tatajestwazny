<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../includes/load_env.php';

ensure_secure_session();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$hash = $_ENV['AUTH_PASSWORD_HASH'] ?? getenv('AUTH_PASSWORD_HASH');
if (!$hash) {
    http_response_code(500);
    echo '❌ Brakuje zmiennej środowiskowej AUTH_PASSWORD_HASH.';
    exit;
}

if (($_GET['action'] ?? null) === 'logout') {
    logout();
    header('Location: /admin/auth.php');
    exit;
}

$attemptState = $_SESSION['auth_attempts'] ?? ['count' => 0, 'locked_until' => 0];
$now = time();

if (!empty($attemptState['locked_until']) && $attemptState['locked_until'] <= $now) {
    $attemptState = ['count' => 0, 'locked_until' => 0];
    $_SESSION['auth_attempts'] = $attemptState;
}

$error = null;

if ($attemptState['locked_until'] > $now) {
    $remaining = $attemptState['locked_until'] - $now;
    $error = 'Zbyt wiele prób logowania. Spróbuj ponownie za ' . max(1, $remaining) . ' sekund.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    require_post_csrf();

    $password = $_POST['password'] ?? '';
    if (password_verify($password, $hash)) {
        force_regenerate_session();
        $_SESSION['logged_in'] = true;
        $_SESSION['logged_in_at'] = $now;
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
        unset($_SESSION['auth_attempts']);
        header('Location: /admin/');
        exit;
    }

    $attemptState['count'] = ($attemptState['count'] ?? 0) + 1;
    if ($attemptState['count'] >= 5) {
        $attemptState['locked_until'] = $now + 300;
        $error = 'Zbyt wiele prób logowania. Spróbuj ponownie za 300 sekund.';
    } else {
        $error = 'Nieprawidłowe hasło. Spróbuj ponownie.';
    }
    $_SESSION['auth_attempts'] = $attemptState;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $error) {
    require_post_csrf();
}

$csrfToken = $_SESSION['csrf'];
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logowanie • Panel administracyjny</title>
  <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
  <style>
    :focus-visible{outline:3px solid #fb923c;outline-offset:2px}
  </style>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-white/95 backdrop-blur rounded-3xl shadow-soft p-8 space-y-6">
    <header class="space-y-2 text-center">
      <p class="text-xs uppercase tracking-[0.3em] text-orange-500 font-semibold">Panel administracyjny</p>
      <h1 class="text-2xl font-semibold text-slate-900">Zaloguj się</h1>
      <p class="text-sm text-slate-500">Podaj hasło, aby zarządzać treściami serwisu.</p>
    </header>
    <?php if ($error): ?>
      <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm flex items-start gap-3">
        <span class="text-lg">⚠️</span>
        <p><?= htmlspecialchars($error) ?></p>
      </div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
      <label class="block text-sm font-medium text-slate-600">Hasło
        <input type="password" name="password" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 shadow-inner focus:border-orange-400 focus:ring-0" placeholder="••••••••" required>
      </label>
      <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-orange-500 text-white font-semibold shadow-soft hover:bg-orange-600 transition"<?= $error && $attemptState['locked_until'] > $now ? ' disabled' : '' ?>>Zaloguj się</button>
    </form>
    <p class="text-xs text-center text-slate-400">Masz problem z logowaniem? Skontaktuj się z opiekunem technicznym.</p>
  </div>
</body>
</html>
