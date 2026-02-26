<?php
session_start();

require_once __DIR__ . '/MysqliDb.php';
require_once __DIR__ . '/mysql.php';

if (empty($_SESSION['engineer']['id'])) {
    header('Location: ./index.php');
    exit;
}

function utf8Length($value)
{
    $value = (string) $value;
    if ($value === '') {
        return 0;
    }
    if (preg_match_all('/./u', $value, $matches) !== false) {
        return count($matches[0]);
    }
    return strlen($value);
}

$acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
$isZhTw = strpos($acceptLanguage, 'zh-tw') !== false || strpos($acceptLanguage, 'zh-hant') !== false;

$t = $isZhTw
    ? [
        'lang' => 'zh-Hant',
        'title' => '工程師資料',
        'country' => '國家',
        'name_placeholder' => '請輸入姓名（至少 2 字元）',
        'email_placeholder' => '請輸入 Email',
        'linkedin_placeholder' => 'LinkedIn 連結（選填）',
        'phone_placeholder' => '電話（選填）',
        'web_placeholder' => 'Web URL（選填）',
        'whatsapp_placeholder' => 'WhatsApp（選填）',
        'line_placeholder' => 'Line（選填）',
        'new_project_only_label' => '只接受全新的開發專案',
        'min_budget_label' => '僅接受多少預算以上的專案（USD）',
        'min_budget_placeholder' => '例如 3000',
        'name_error' => '姓名至少需要 2 個字元。',
        'email_error' => '請輸入有效的 Email。',
        'url_error' => 'URL 格式不正確，請輸入完整網址（例如 https://example.com）。',
        'updated' => '資料已更新。',
        'update' => 'Update',
      ]
    : [
        'lang' => 'en',
        'title' => 'Engineer Profile',
        'country' => 'Country',
        'name_placeholder' => 'Name (at least 2 characters)',
        'email_placeholder' => 'Email',
        'linkedin_placeholder' => 'LinkedIn URL (optional)',
        'phone_placeholder' => 'Phone (optional)',
        'web_placeholder' => 'Web URL (optional)',
        'whatsapp_placeholder' => 'WhatsApp (optional)',
        'line_placeholder' => 'Line (optional)',
        'new_project_only_label' => 'Accept new development projects only',
        'min_budget_label' => 'Minimum accepted project budget (USD)',
        'min_budget_placeholder' => 'e.g. 3000',
        'name_error' => 'Name must be at least 2 characters.',
        'email_error' => 'Please enter a valid email address.',
        'url_error' => 'Invalid URL format. Please use a full URL like https://example.com.',
        'updated' => 'Profile updated.',
        'update' => 'Update',
      ];

$countries = [
    'US' => ['zh' => '美國', 'en' => 'United States'],
    'TW' => ['zh' => '台灣', 'en' => 'Taiwan'],
    'JP' => ['zh' => '日本', 'en' => 'Japan'],
    'KR' => ['zh' => '韓國', 'en' => 'South Korea'],
    'SG' => ['zh' => '新加坡', 'en' => 'Singapore'],
    'HK' => ['zh' => '香港', 'en' => 'Hong Kong'],
    'CN' => ['zh' => '中國', 'en' => 'China'],
    'TH' => ['zh' => '泰國', 'en' => 'Thailand'],
    'VN' => ['zh' => '越南', 'en' => 'Vietnam'],
    'MY' => ['zh' => '馬來西亞', 'en' => 'Malaysia'],
    'PH' => ['zh' => '菲律賓', 'en' => 'Philippines'],
    'IN' => ['zh' => '印度', 'en' => 'India'],
    'AU' => ['zh' => '澳洲', 'en' => 'Australia'],
    'NZ' => ['zh' => '紐西蘭', 'en' => 'New Zealand'],
    'CA' => ['zh' => '加拿大', 'en' => 'Canada'],
    'GB' => ['zh' => '英國', 'en' => 'United Kingdom'],
    'DE' => ['zh' => '德國', 'en' => 'Germany'],
    'FR' => ['zh' => '法國', 'en' => 'France'],
    'NL' => ['zh' => '荷蘭', 'en' => 'Netherlands'],
    'BR' => ['zh' => '巴西', 'en' => 'Brazil'],
];

$dialCodes = [
    'US' => '+1', 'TW' => '+886', 'JP' => '+81', 'KR' => '+82', 'SG' => '+65',
    'HK' => '+852', 'CN' => '+86', 'TH' => '+66', 'VN' => '+84', 'MY' => '+60',
    'PH' => '+63', 'IN' => '+91', 'AU' => '+61', 'NZ' => '+64', 'CA' => '+1',
    'GB' => '+44', 'DE' => '+49', 'FR' => '+33', 'NL' => '+31', 'BR' => '+55',
];

$engineerId = (int) $_SESSION['engineer']['id'];
$db->where('id', $engineerId);
$engineer = $db->getOne('engineers');

if (!$engineer) {
    unset($_SESSION['engineer']);
    header('Location: ./index.php');
    exit;
}

$current = [
    'country' => strtoupper((string) ($engineer['country'] ?? 'US')),
    'name' => trim((string) ($engineer['name'] ?? '')),
    'email' => trim((string) ($engineer['email'] ?? '')),
    'linkedin_url' => trim((string) ($engineer['linkedin_url'] ?? '')),
    'phone_country_code' => trim((string) ($engineer['phone_country_code'] ?? '')),
    'phone' => trim((string) ($engineer['phone'] ?? '')),
    'web_url' => trim((string) ($engineer['web_url'] ?? '')),
    'whatsapp' => trim((string) ($engineer['whatsapp'] ?? '')),
    'line_id' => trim((string) ($engineer['line_id'] ?? '')),
    'new_project_only' => (int) ($engineer['new_project_only'] ?? 0),
    'min_project_budget_usd' => (int) ($engineer['min_project_budget_usd'] ?? 0),
];

if (!isset($countries[$current['country']])) {
    $current['country'] = 'US';
}
if ($current['phone_country_code'] === '') {
    $current['phone_country_code'] = $dialCodes[$current['country']] ?? '+1';
}

$flashMessage = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = [
        'country' => strtoupper((string) ($_POST['country'] ?? $current['country'])),
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'linkedin_url' => trim((string) ($_POST['linkedin_url'] ?? '')),
        'phone_country_code' => trim((string) ($_POST['phone_country_code'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'web_url' => trim((string) ($_POST['web_url'] ?? '')),
        'whatsapp' => trim((string) ($_POST['whatsapp'] ?? '')),
        'line_id' => trim((string) ($_POST['line_id'] ?? '')),
        'new_project_only' => isset($_POST['new_project_only']) ? 1 : 0,
        'min_project_budget_usd' => (string) ($_POST['min_project_budget_usd'] ?? '0'),
    ];

    if (!isset($countries[$posted['country']])) {
        $posted['country'] = $current['country'];
    }

    if (!in_array($posted['phone_country_code'], array_values($dialCodes), true)) {
        $posted['phone_country_code'] = $dialCodes[$posted['country']] ?? '+1';
    }


    $posted['min_project_budget_usd'] = trim((string) $posted['min_project_budget_usd']);
    if ($posted['min_project_budget_usd'] === '' || !preg_match('/^\d+$/', $posted['min_project_budget_usd'])) {
        $posted['min_project_budget_usd'] = '0';
    }
    $posted['min_project_budget_usd'] = (int) $posted['min_project_budget_usd'];

    if (utf8Length($posted['name']) < 2) {
        $errorMessage = $t['name_error'];
    } elseif (!filter_var($posted['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessage = $t['email_error'];
    } elseif ($posted['linkedin_url'] !== '' && !filter_var($posted['linkedin_url'], FILTER_VALIDATE_URL)) {
        $errorMessage = $t['url_error'];
    } elseif ($posted['web_url'] !== '' && !filter_var($posted['web_url'], FILTER_VALIDATE_URL)) {
        $errorMessage = $t['url_error'];
    } else {
        $db->where('id', $engineerId);
        $ok = $db->update('engineers', [
            'country' => $posted['country'],
            'name' => $posted['name'],
            'email' => $posted['email'],
            'linkedin_url' => $posted['linkedin_url'] !== '' ? $posted['linkedin_url'] : null,
            'phone_country_code' => $posted['phone'] !== '' ? $posted['phone_country_code'] : null,
            'phone' => $posted['phone'] !== '' ? $posted['phone'] : null,
            'web_url' => $posted['web_url'] !== '' ? $posted['web_url'] : null,
            'whatsapp' => $posted['whatsapp'] !== '' ? $posted['whatsapp'] : null,
            'line_id' => $posted['line_id'] !== '' ? $posted['line_id'] : null,
            'new_project_only' => $posted['new_project_only'] ? 1 : 0,
            'min_project_budget_usd' => max(0, (int) $posted['min_project_budget_usd']),
        ]);

        if ($ok) {
            $current = $posted;
            $_SESSION['engineer']['name'] = $posted['name'];
            $_SESSION['engineer']['email'] = $posted['email'];
            $flashMessage = $t['updated'];
        }
    }

    if ($errorMessage) {
        $current = $posted;
    }
}
?>
<!doctype html>
<html lang="<?= $t['lang'] ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>neeJou - Engineer Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white text-zinc-900 antialiased transition-colors duration-300 dark:bg-zinc-950 dark:text-zinc-100">
  <?php include __DIR__ . '/client_navbar.php'; ?>

  <main class="mx-auto flex w-full max-w-6xl justify-center px-4 py-8 sm:px-6">
    <section class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white/70 p-6 shadow-sm backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60 sm:p-8">
      <h1 class="text-center text-xl font-semibold tracking-tight sm:text-2xl"><?= htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8') ?></h1>

      <form method="post" class="mt-6 space-y-4">
        <div>
          <label for="country" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            <?= htmlspecialchars($t['country'], ENT_QUOTES, 'UTF-8') ?>
          </label>
          <select id="country" name="country" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
            <?php foreach ($countries as $code => $label): ?>
              <?php $countryName = $isZhTw ? $label['zh'] : $label['en']; ?>
              <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= $code === $current['country'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <input type="text" name="name" value="<?= htmlspecialchars($current['name'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['name_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />

        <input type="email" name="email" value="<?= htmlspecialchars($current['email'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['email_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />

        <input type="url" name="linkedin_url" value="<?= htmlspecialchars($current['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['linkedin_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />

        <div class="grid grid-cols-3 gap-2">
          <select name="phone_country_code" class="rounded-xl border border-zinc-300 bg-white px-3 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
            <?php foreach ($dialCodes as $countryCode => $dial): ?>
              <option value="<?= htmlspecialchars($dial, ENT_QUOTES, 'UTF-8') ?>" <?= $dial === $current['phone_country_code'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($countryCode . ' ' . $dial, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="text" name="phone" value="<?= htmlspecialchars($current['phone'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['phone_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="col-span-2 rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
        </div>

        <input type="url" name="web_url" value="<?= htmlspecialchars($current['web_url'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['web_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />

        <input type="text" name="whatsapp" value="<?= htmlspecialchars($current['whatsapp'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['whatsapp_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />

        <input type="text" name="line_id" value="<?= htmlspecialchars($current['line_id'], ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['line_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />


        <label class="inline-flex items-center gap-3 text-sm text-zinc-700 dark:text-zinc-300">
          <input type="checkbox" name="new_project_only" value="1" class="h-4 w-4" <?= !empty($current['new_project_only']) ? 'checked' : '' ?> />
          <span><?= htmlspecialchars($t['new_project_only_label'], ENT_QUOTES, 'UTF-8') ?></span>
        </label>

        <div>
          <label for="min-project-budget-usd" class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($t['min_budget_label'], ENT_QUOTES, 'UTF-8') ?></label>
          <input id="min-project-budget-usd" type="number" min="0" step="1" name="min_project_budget_usd" value="<?= htmlspecialchars((string) ($current['min_project_budget_usd'] ?? 0), ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars($t['min_budget_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm font-medium text-zinc-800 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">
          <?= htmlspecialchars($t['update'], ENT_QUOTES, 'UTF-8') ?>
        </button>
      </form>

      <?php if ($errorMessage): ?>
        <p class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300">
          <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>

      <?php if ($flashMessage): ?>
        <p class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
          <?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
