<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$sessionType = null;
if (!empty($_SESSION['client']['id'])) {
    $sessionType = 'client';
} elseif (!empty($_SESSION['engineer']['id'])) {
    $sessionType = 'engineer';
}

if ($sessionType === null) {
    header('Location: ./index.php');
    exit;
}

$acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
$isZhTw = strpos($acceptLanguage, 'zh-tw') !== false || strpos($acceptLanguage, 'zh-hant') !== false;

$navText = $isZhTw
    ? ['dashboard' => '關於 neeJou', 'profile' => '個人資料', 'logout' => '登出']
    : ['dashboard' => 'About neeJou', 'profile' => 'Profile', 'logout' => 'Logout'];

if ($sessionType === 'client') {
    $navUserName = trim((string) ($_SESSION['client']['name'] ?? $_SESSION['client']['email'] ?? 'User'));
    $navAvatar = trim((string) ($_SESSION['client']['picture'] ?? ''));
    $dashboardLink = './client_dashboard.php';
    $profileLink = './client_profile.php';
} else {
    $navUserName = trim((string) ($_SESSION['engineer']['name'] ?? $_SESSION['engineer']['username'] ?? $_SESSION['engineer']['email'] ?? 'Engineer'));
    $navAvatar = trim((string) ($_SESSION['engineer']['avatar'] ?? ''));
    $dashboardLink = './engineer_dashboard.php';
    $profileLink = './engineer_profile.php';
}

$navInitial = strtoupper(substr($navUserName !== '' ? $navUserName : 'U', 0, 1));
?>
<header class="sticky top-0 z-30 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">
  <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
    <a href="<?= htmlspecialchars($dashboardLink, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center">
      <img src="./images/logo.webp" alt="neeJou Logo" class="h-10 w-auto object-contain sm:h-12" />
    </a>

    <div class="relative">
      <button
        id="nav-user-btn"
        type="button"
        class="inline-flex items-center gap-2 rounded-full border border-zinc-300 bg-white px-2 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
      >
        <?php if ($navAvatar !== ''): ?>
          <img src="<?= htmlspecialchars($navAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" class="h-8 w-8 rounded-full object-cover" />
        <?php else: ?>
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-zinc-200 text-xs font-semibold text-zinc-700 dark:bg-zinc-700 dark:text-zinc-100"><?= htmlspecialchars($navInitial, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
        <span class="max-w-[120px] truncate"><?= htmlspecialchars($navUserName, ENT_QUOTES, 'UTF-8') ?></span>
        <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
      </button>

      <div id="nav-user-menu" class="absolute right-0 mt-2 hidden min-w-[170px] rounded-xl border border-zinc-200 bg-white p-1 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <a href="<?= htmlspecialchars($dashboardLink, ENT_QUOTES, 'UTF-8') ?>" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($navText['dashboard'], ENT_QUOTES, 'UTF-8') ?></a>
        <a href="<?= htmlspecialchars($profileLink, ENT_QUOTES, 'UTF-8') ?>" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($navText['profile'], ENT_QUOTES, 'UTF-8') ?></a>
        <a href="./logout.php" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($navText['logout'], ENT_QUOTES, 'UTF-8') ?></a>
      </div>
    </div>
  </div>
</header>
<script>
  (function () {
    var btn = document.getElementById('nav-user-btn');
    var menu = document.getElementById('nav-user-menu');
    if (!btn || !menu) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      menu.classList.toggle('hidden');
    });

    document.addEventListener('click', function () {
      menu.classList.add('hidden');
    });
  })();
</script>
