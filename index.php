<?php
session_start();

require_once __DIR__ . '/MysqliDb.php';
require_once __DIR__ . '/mysql.php';

if (!empty($_SESSION['client']['id']) && (($_SESSION['client']['provider'] ?? 'google') === 'google')) {
    header('Location: ./client_dashboard.php');
    exit;
}

if (!empty($_SESSION['engineer']['id']) && (($_SESSION['engineer']['provider'] ?? 'github') === 'github')) {
    header('Location: ./engineer_dashboard.php');
    exit;
}

$googleClientId = '260673809767-ao7adnp7o5d3gs277tkd39u94sbm9mqa.apps.googleusercontent.com';
$githubClientId = 'Ov23li8ccObFfoQ0VGgM';

function appBaseUrl(): string
{
    $https = $_SERVER['HTTPS'] ?? '';
    $scheme = (!empty($https) && $https !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    if ($dir === '' || $dir === '.') {
        $dir = '';
    }

    return $scheme . '://' . $host . $dir;
}

function decodeJwtPayload(string $jwt): ?array
{
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return null;
    }

    $payload = strtr($parts[1], '-_', '+/');
    $padding = strlen($payload) % 4;
    if ($padding > 0) {
        $payload .= str_repeat('=', 4 - $padding);
    }

    $decoded = base64_decode($payload, true);
    if ($decoded === false) {
        return null;
    }

    $data = json_decode($decoded, true);
    return is_array($data) ? $data : null;
}

function neejou_finish_google_login(MysqliDb $db, string $googleId, string $email, string $googleName, string $picture): array
{
    $googleId = trim($googleId);
    $email = trim($email);
    $googleName = trim($googleName);
    $picture = trim($picture);

    if ($googleId === '') {
        return ['ok' => false, 'message' => 'invalid_google_user'];
    }

    if ($email === '') {
        $email = $googleId . '@google.local';
    }

    $now = date('Y-m-d H:i:s');
    $isNewClient = false;

    $db->where('google_id', $googleId);
    $client = $db->getOne('clients');

    if (!$client) {
        $isNewClient = true;
        $clientId = $db->insert('clients', [
            'google_id' => $googleId,
            'email' => $email,
            'name' => $googleName !== '' ? $googleName : null,
            'country' => 'US',
            'last_login_at' => $now,
        ]);
        if (!$clientId) {
            return ['ok' => false, 'message' => 'insert_failed'];
        }
    } else {
        $clientId = (int) $client['id'];
        $updateData = ['last_login_at' => $now];
        if ($email !== '') {
            $updateData['email'] = $email;
        }
        if (empty($client['name']) && $googleName !== '') {
            $updateData['name'] = $googleName;
        }

        $db->where('id', $clientId);
        $db->update('clients', $updateData);
    }

    $db->where('id', (int) $clientId);
    $clientRow = $db->getOne('clients');

    if (!$clientRow) {
        return ['ok' => false, 'message' => 'load_failed'];
    }

    $_SESSION['client'] = [
        'id' => (int) $clientRow['id'],
        'google_id' => (string) $clientRow['google_id'],
        'email' => (string) $clientRow['email'],
        'name' => (string) ($clientRow['name'] ?: $clientRow['email']),
        'picture' => $picture,
        'provider' => 'google',
    ];

    return [
        'ok' => true,
        'redirect' => $isNewClient ? './client_profile.php' : './client_dashboard.php',
    ];
}

$acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
$isZhTw = strpos($acceptLanguage, 'zh-tw') !== false || strpos($acceptLanguage, 'zh-hant') !== false;

$t = $isZhTw
    ? [
        'lang' => 'zh-Hant',
        'title' => 'AI 工程師媒合',
        'login_hint' => '請選擇登入方式開始使用',
        'login_error' => 'Google 登入失敗，請再試一次。',
        'client_login_note' => '發案專案用戶請使用 Google 登入',
        'engineer_login_note' => '工程師接案請使用 Git 登入',
      ]
    : [
        'lang' => 'en',
        'title' => 'AI Engineer Matching',
        'login_hint' => 'Choose a sign-in method to continue',
        'login_error' => 'Google sign-in failed. Please try again.',
        'client_login_note' => 'Project owners should sign in with Google',
        'engineer_login_note' => 'Engineers should sign in with Git',
      ];

$loginError = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'google_login')) {
    $credential = trim((string) ($_POST['credential'] ?? ''));
    $payload = decodeJwtPayload($credential);

    if (!$payload || ($payload['aud'] ?? '') !== $googleClientId || empty($payload['sub'])) {
        $loginError = $t['login_error'];
    } else {
        $googleId = (string) $payload['sub'];
        $email = (string) ($payload['email'] ?? '');
        $googleName = trim((string) ($payload['name'] ?? ''));
        $picture = (string) ($payload['picture'] ?? '');

        $result = neejou_finish_google_login($db, $googleId, $email, $googleName, $picture);
        if (!$result['ok']) {
            $loginError = $t['login_error'];
        } else {
            header('Location: ' . $result['redirect']);
            exit;
        }
    }
}

$githubState = bin2hex(random_bytes(16));
$_SESSION['github_oauth_state'] = $githubState;
$githubRedirectUri = appBaseUrl() . '/git_callback.php';
$githubAuthUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
    'client_id' => $githubClientId,
    'redirect_uri' => $githubRedirectUri,
    'scope' => 'read:user user:email',
    'state' => $githubState,
]);
?>
<!doctype html>
<html lang="<?= htmlspecialchars($t['lang'], ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>neeJou</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="min-h-screen bg-white text-zinc-900 antialiased transition-colors duration-300 dark:bg-zinc-950 dark:text-zinc-100">
  <main class="flex min-h-screen items-center justify-center px-4 py-10">
    <section class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white/70 p-6 text-center shadow-sm backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60 sm:p-8">
      <img src="./images/logo.webp" alt="neeJou Logo" class="mx-auto w-full max-w-[400px] h-auto object-contain border-0 ring-0 shadow-none" />

      <h1 class="mt-5 text-xl font-semibold tracking-tight sm:text-2xl"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400"><?= htmlspecialchars($t['login_hint'], ENT_QUOTES, 'UTF-8') ?></p>

      <?php if ($loginError): ?>
        <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-950/40 dark:text-red-300">
          <?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>

      <div class="mt-6 grid gap-3">
        <form id="google-login-form" method="post" action="./index.php" class="w-full">
          <input type="hidden" name="action" value="google_login" />
          <input type="hidden" name="credential" id="google_credential" value="" />
        </form>

        <div id="google-signin" class="flex w-full justify-center"></div>
        <p class="-mt-1 text-xs text-zinc-500 dark:text-zinc-400">
          <?= htmlspecialchars($t['client_login_note'], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <a
          href="<?= htmlspecialchars($githubAuthUrl, ENT_QUOTES, 'UTF-8') ?>"
          class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm font-medium text-zinc-800 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800"
        >
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
            <path d="M12 .5C5.7.5.7 5.6.7 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.3.8-.6v-2.2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.4-1.3-1.7-1.3-1.7-1.1-.8.1-.8.1-.8 1.2.1 1.9 1.3 1.9 1.3 1.1 1.9 2.9 1.4 3.6 1.1.1-.8.4-1.4.8-1.7-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.4 1.3-3.3-.1-.3-.6-1.5.1-3.1 0 0 1.1-.3 3.4 1.3 1-.3 2-.4 3-.4 1 0 2 .1 3 .4 2.3-1.6 3.4-1.3 3.4-1.3.7 1.6.3 2.8.1 3.1.8.9 1.3 2 1.3 3.3 0 4.5-2.7 5.5-5.3 5.8.4.3.8 1 .8 2.1v3.1c0 .3.2.7.8.6 4.6-1.5 7.9-5.8 7.9-10.9C23.3 5.6 18.3.5 12 .5z"/>
          </svg>
          GitHub
        </a>
        <p class="-mt-1 text-xs text-zinc-500 dark:text-zinc-400">
          <?= htmlspecialchars($t['engineer_login_note'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>
    </section>
  </main>

  <script>
    function handleGoogleCredential(response) {
      if (!response || !response.credential) return;
      document.getElementById('google_credential').value = response.credential;
      document.getElementById('google-login-form').submit();
    }

    function renderGoogleButton() {
      if (!window.google || !google.accounts || !google.accounts.id) return;
      var target = document.getElementById('google-signin');
      if (!target) return;
      target.innerHTML = '';
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

      google.accounts.id.renderButton(target, {
        theme: prefersDark ? 'filled_black' : 'outline',
        size: 'large',
        shape: 'pill',
        width: 320,
        text: 'signin_with'
      });
    }

    window.addEventListener('load', function () {
      if (!window.google || !google.accounts || !google.accounts.id) return;

      google.accounts.id.initialize({
        client_id: '260673809767-ao7adnp7o5d3gs277tkd39u94sbm9mqa.apps.googleusercontent.com',
        callback: handleGoogleCredential
      });

      renderGoogleButton();

      if (window.matchMedia) {
        var mq = window.matchMedia('(prefers-color-scheme: dark)');
        if (typeof mq.addEventListener === 'function') {
          mq.addEventListener('change', renderGoogleButton);
        } else if (typeof mq.addListener === 'function') {
          mq.addListener(renderGoogleButton);
        }
      }
    });
  </script>
</body>
</html>
