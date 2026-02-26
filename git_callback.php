<?php
session_start();

require_once __DIR__ . '/MysqliDb.php';
require_once __DIR__ . '/mysql.php';

$githubClientId = 'Ov23li8ccObFfoQ0VGgM';
$githubClientSecret = 'ba80c8de70be0222bf463dc6829724192be507ce';

function appBaseUrl()
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

function githubApiGet($url, $token)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github+json',
            'Authorization: Bearer ' . $token,
            'User-Agent: neejou-app',
            'X-GitHub-Api-Version: 2022-11-28',
        ],
        CURLOPT_TIMEOUT => 20,
    ]);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300 || !$response) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

$state = (string) ($_GET['state'] ?? '');
$expectedState = (string) ($_SESSION['github_oauth_state'] ?? '');
$code = (string) ($_GET['code'] ?? '');
unset($_SESSION['github_oauth_state']);

if ($state === '' || $expectedState === '' || !hash_equals($expectedState, $state) || $code === '') {
    header('Location: ./index.php');
    exit;
}

$tokenUrl = 'https://github.com/login/oauth/access_token';
$redirectUri = appBaseUrl() . '/git_callback.php';

$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'User-Agent: neejou-app',
    ],
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $githubClientId,
        'client_secret' => $githubClientSecret,
        'code' => $code,
        'redirect_uri' => $redirectUri,
        'state' => $state,
    ]),
    CURLOPT_TIMEOUT => 20,
]);

$tokenResponse = curl_exec($ch);
$tokenHttpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenHttpCode < 200 || $tokenHttpCode >= 300 || !$tokenResponse) {
    header('Location: ./index.php');
    exit;
}

$tokenData = json_decode($tokenResponse, true);
$accessToken = is_array($tokenData) ? (string) ($tokenData['access_token'] ?? '') : '';

if ($accessToken === '') {
    header('Location: ./index.php');
    exit;
}

$githubUser = githubApiGet('https://api.github.com/user', $accessToken);
if (!$githubUser || empty($githubUser['id']) || empty($githubUser['login'])) {
    header('Location: ./index.php');
    exit;
}

$emails = githubApiGet('https://api.github.com/user/emails', $accessToken);
$primaryEmail = '';
if (is_array($emails)) {
    foreach ($emails as $item) {
        if (!is_array($item)) {
            continue;
        }
        if (!empty($item['primary']) && !empty($item['verified']) && !empty($item['email'])) {
            $primaryEmail = (string) $item['email'];
            break;
        }
    }
}

if ($primaryEmail === '' && !empty($githubUser['email'])) {
    $primaryEmail = (string) $githubUser['email'];
}

$gitId = (string) $githubUser['login'];
$gitName = trim((string) ($githubUser['name'] ?? ''));
if ($gitName === '') {
    $gitName = $gitId;
}
$avatar = (string) ($githubUser['avatar_url'] ?? '');
$now = date('Y-m-d H:i:s');
$isNewEngineer = false;

$db->where('git_id', $gitId);
$engineer = $db->getOne('engineers');

if (!$engineer) {
    $isNewEngineer = true;
    $engineerId = $db->insert('engineers', [
        'git_id' => $gitId,
        'name' => $gitName,
        'email' => $primaryEmail !== '' ? $primaryEmail : null,
        'country' => 'US',
        'last_login_at' => $now,
    ]);

    if (!$engineerId) {
        header('Location: ./index.php');
        exit;
    }
} else {
    $engineerId = (int) $engineer['id'];
    $updateData = ['last_login_at' => $now];
    if ($primaryEmail !== '') {
        $updateData['email'] = $primaryEmail;
    }
    if (empty($engineer['name']) && $gitName !== '') {
        $updateData['name'] = $gitName;
    }

    $db->where('id', $engineerId);
    $db->update('engineers', $updateData);
}

$db->where('id', (int) $engineerId);
$engineerRow = $db->getOne('engineers');
if (!$engineerRow) {
    header('Location: ./index.php');
    exit;
}

$_SESSION['engineer'] = [
    'id' => (int) $engineerRow['id'],
    'git_id' => (string) $engineerRow['git_id'],
    'email' => (string) ($engineerRow['email'] ?? ''),
    'name' => (string) ($engineerRow['name'] ?? $gitName),
    'username' => $gitId,
    'avatar' => $avatar,
    'provider' => 'github',
];

if ($isNewEngineer) {
    header('Location: ./engineer_profile.php');
} else {
    header('Location: ./engineer_dashboard.php');
}
exit;
