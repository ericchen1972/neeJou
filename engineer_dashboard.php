<?php
session_start();
require_once __DIR__ . '/MysqliDb.php';
require_once __DIR__ . '/mysql.php';

if (empty($_SESSION['engineer']['id'])) {
    header('Location: ./index.php');
    exit;
}

$acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
$isZhTw = strpos($acceptLanguage, 'zh-tw') !== false || strpos($acceptLanguage, 'zh-hant') !== false;

$t = $isZhTw
    ? [
        'lang' => 'zh-Hant',
        'dashboard' => '關於 neeJou',
        'add_git_project' => '加入 Git 專案',
        'my_projects' => '我的專案',
        'title' => '關於 neeJou',
        'hello' => '歡迎回來，',
        'note' => '這裡是工程師主頁，後續可查看媒合結果與任務。',
        'my_projects_note' => '目前尚未加入任何 Git 專案。',
        'profile' => '個人資料',
        'logout' => '登出',
        'modal_title' => '加入 Git 專案',
        'repo_url' => 'Repo URL',
        'languages' => 'Languages',
        'databases' => 'Databases',
        'keywords' => 'KeyWords',
        'close' => '關閉',
        'done' => '完成',
        'save_hint' => '請正確選擇專案所使用的語言、資料庫以及專案的類別，才不會錯失配對機會。',
        'repo_url_placeholder' => 'https://github.com/username/repo',
        'search_lang_placeholder' => '輸入語言並選擇',
        'search_db_placeholder' => '輸入資料庫並選擇',
        'search_keyword_placeholder' => '輸入關鍵字並選擇',
        'added_demo' => '已完成（Demo，尚未儲存）',
        'unnamed_engineer' => 'Engineer',
        'validation_repo_url' => '請輸入有效的 GitHub Repo URL。',
        'validation_languages' => '請至少選擇一個語言。',
        'submitting' => '執行中...',
        'project_name_col' => '專案名稱',
        'languages_col' => '語言',
        'databases_col' => '資料庫',
        'keywords_col' => '關鍵字',
        'actions_col' => '操作',
        'edit' => '編輯',
        'delete' => '刪除',
        'no_repo_projects' => '目前尚未加入任何 Git 專案。',
        'confirm_delete_repo' => '確定要刪除這個專案嗎？',
        'load_repo_failed' => '載入專案資料失敗，請稍後再試。',
      ]
    : [
        'lang' => 'en',
        'dashboard' => 'About neeJou',
        'add_git_project' => 'Add Git Project',
        'my_projects' => 'My Projects',
        'title' => 'About neeJou',
        'hello' => 'Welcome back, ',
        'note' => 'This is your engineer home. Matching results and tasks can be shown here later.',
        'my_projects_note' => 'No Git projects added yet.',
        'profile' => 'Profile',
        'logout' => 'Logout',
        'modal_title' => 'Add Git Project',
        'repo_url' => 'Repo URL',
        'languages' => 'Languages',
        'databases' => 'Databases',
        'keywords' => 'KeyWords',
        'close' => 'Close',
        'done' => 'Done',
        'save_hint' => 'Please correctly select the project languages, databases, and categories to avoid missing matching opportunities.',
        'repo_url_placeholder' => 'https://github.com/username/repo',
        'search_lang_placeholder' => 'Type language and select',
        'search_db_placeholder' => 'Type database and select',
        'search_keyword_placeholder' => 'Type keyword and select',
        'added_demo' => 'Done (Demo, not saved yet)',
        'unnamed_engineer' => 'Engineer',
        'validation_repo_url' => 'Please enter a valid GitHub repo URL.',
        'validation_languages' => 'Please select at least one language.',
        'submitting' => 'Running...',
        'project_name_col' => 'Project Name',
        'languages_col' => 'Languages',
        'databases_col' => 'Databases',
        'keywords_col' => 'Keywords',
        'actions_col' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_repo_projects' => 'No Git projects yet.',
        'confirm_delete_repo' => 'Delete this project?',
        'load_repo_failed' => 'Failed to load project details. Please try again.',
      ];

$aboutHtml = $isZhTw
    ? <<<'HTML'
<div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/60 md:grid md:grid-cols-3">
  <div class="hidden min-h-[320px] bg-center bg-cover md:col-span-1 md:block lg:min-h-[420px]" style="background-image:url('./images/neejou2.webp');"></div>
  <div class="p-5 sm:p-7 md:col-span-2">
  <h1 class="text-2xl font-semibold">neeJou</h1>
  <p class="mt-2 text-lg font-medium text-zinc-700 dark:text-zinc-200">讓你的作品替你說話</p>
  <div class="mt-6 space-y-5 text-zinc-700 dark:text-zinc-300">
    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">你是不是也累了？</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>一直投履歷，卻石沉大海</li>
        <li>面試八股，和實作無關</li>
        <li>需求混亂、預算混亂、一直改</li>
        <li>做得很多，卻不被真正理解</li>
      </ul>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">neeJou 不看履歷，我們看 Git</h2>
      <p class="mt-2">不需要堆關鍵字，也不需要花俏自介。只要你有 Git 專案，AI 會讀你的結構、README、commit 與實作內容，判斷你是否適合某個案子。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">不再被低品質需求消耗</h2>
      <p class="mt-2">neeJou 先用 AI 協助業主整理需求，等基礎穩定再配對。你不必再接「需求模糊、預算極低、無限修改」的案子。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">是真正媒合，不是亂槍打鳥</h2>
      <p class="mt-2">當你被推薦，不是因為剛好在線，而是因為你的作品與專案真正對得上。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">你只需要做一件事</h2>
      <p class="mt-2 font-medium">把你的 Repo 加入</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">neeJou 的信念</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>技術應該被理解</li>
        <li>工程師不該靠話術生存</li>
        <li>作品比履歷更誠實</li>
      </ul>
    </section>
  </div>
  </div>
</div>
HTML
    : <<<'HTML'
<div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/60 md:grid md:grid-cols-3">
  <div class="hidden min-h-[320px] bg-center bg-cover md:col-span-1 md:block lg:min-h-[420px]" style="background-image:url('./images/neejou2.webp');"></div>
  <div class="p-5 sm:p-7 md:col-span-2">
  <h1 class="text-2xl font-semibold">neeJou</h1>
  <p class="mt-2 text-lg font-medium text-zinc-700 dark:text-zinc-200">Let your work speak for you</p>
  <div class="mt-6 space-y-5 text-zinc-700 dark:text-zinc-300">
    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Tired of the same cycle?</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>Sending resumes with no response</li>
        <li>Interview trivia unrelated to real work</li>
        <li>Chaotic requirements and unrealistic budgets</li>
        <li>Doing more while being understood less</li>
      </ul>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">neeJou reads repos, not resume buzzwords</h2>
      <p class="mt-2">No keyword stuffing, no polished self-promotion required. If you have real Git projects, AI reads your structure, README, commits, and implementation details to evaluate fit.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Less time wasted on poor projects</h2>
      <p class="mt-2">neeJou helps clients stabilize project requirements first, then starts matching. You avoid low-budget, unclear, endlessly changing projects.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Real matching, not random leads</h2>
      <p class="mt-2">When you are recommended, it is not because you are available. It is because your work genuinely matches the project.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">You only need one action</h2>
      <p class="mt-2 font-medium">Add your repository</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">What neeJou believes</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>Technical ability should be understood correctly</li>
        <li>Engineers should not rely on sales talk</li>
        <li>Work is more honest than resumes</li>
      </ul>
    </section>
  </div>
  </div>
</div>
HTML;

$engineerId = (int) ($_SESSION['engineer']['id'] ?? 0);

function neejou_normalize_label(string $v): string
{
    $v = strtolower(trim($v));
    return preg_replace('/[^a-z0-9]+/', '', $v) ?? '';
}

function neejou_parse_github_repo(string $url): ?array
{
    $url = trim($url);
    if ($url === '') return null;
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    $parts = parse_url($url);
    if (!is_array($parts)) return null;

    $host = strtolower((string) ($parts['host'] ?? ''));
    if (!in_array($host, ['github.com', 'www.github.com'], true)) return null;

    $path = trim((string) ($parts['path'] ?? ''), '/');
    if ($path === '') return null;
    $segs = explode('/', $path);
    if (count($segs) < 2) return null;

    $owner = trim((string) ($segs[0] ?? ''));
    $repo = trim((string) ($segs[1] ?? ''));
    $repo = preg_replace('/\.git$/i', '', $repo) ?? $repo;

    if ($owner === '' || $repo === '') return null;
    return ['owner' => $owner, 'repo' => $repo];
}

function neejou_http_get_json(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github+json',
            'User-Agent: neejou-app',
        ],
    ]);

    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if (!is_string($raw) || $raw === '') {
        return ['status' => $status, 'data' => null, 'error' => $err !== '' ? $err : 'empty_response'];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return ['status' => $status, 'data' => null, 'error' => 'invalid_json'];
    }

    return ['status' => $status, 'data' => $data, 'error' => ''];
}

function neejou_map_labels_to_ids(array $rows, array $labels): array
{
    $map = [];
    foreach ($rows as $row) {
        if (!is_array($row)) continue;
        $id = (int) ($row['id'] ?? 0);
        if ($id <= 0) continue;
        $display = neejou_normalize_label((string) ($row['display_name'] ?? ''));
        $code = neejou_normalize_label((string) ($row['code'] ?? ''));
        if ($display !== '') $map[$display] = $id;
        if ($code !== '') $map[$code] = $id;
    }

    $ids = [];
    $missing = [];
    foreach ($labels as $label) {
        $k = neejou_normalize_label((string) $label);
        if ($k === '') continue;
        if (!isset($map[$k])) {
            $missing[] = (string) $label;
            continue;
        }
        $ids[] = (int) $map[$k];
    }

    $ids = array_values(array_unique(array_filter($ids, static fn($v) => $v > 0)));
    return ['ids' => $ids, 'missing' => $missing];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === '') {
        $rawInput = file_get_contents('php://input');
        if (is_string($rawInput) && $rawInput !== '') {
            $jsonInput = json_decode($rawInput, true);
            if (is_array($jsonInput)) {
                $action = (string) ($jsonInput['action'] ?? '');
                $_POST = array_merge($_POST, $jsonInput);
            }
        }
    }

    if ($action === 'add_git_repo') {
        header('Content-Type: application/json; charset=UTF-8');

        $repoIdInput = (int) ($_POST['repo_id'] ?? 0);
        $repoUrl = trim((string) ($_POST['repo_url'] ?? ''));
        $languages = $_POST['languages'] ?? [];
        $databases = $_POST['databases'] ?? [];
        $keywords = $_POST['keywords'] ?? [];

        if (!is_array($languages)) $languages = [];
        if (!is_array($databases)) $databases = [];
        if (!is_array($keywords)) $keywords = [];

        $languages = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $languages), static fn($v) => $v !== ''));
        $databases = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $databases), static fn($v) => $v !== ''));
        $keywords = array_values(array_filter(array_map(static fn($v) => trim((string) $v), $keywords), static fn($v) => $v !== ''));

        $repoInfo = neejou_parse_github_repo($repoUrl);
        if (!$repoInfo) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => $isZhTw ? '請輸入有效的 GitHub Repo URL。' : 'Please enter a valid GitHub repo URL.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($languages)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => $isZhTw ? '請至少選擇一個語言。' : 'Please select at least one language.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $owner = (string) $repoInfo['owner'];
        $repo = (string) $repoInfo['repo'];
        $currentGitUser = strtolower(trim((string) ($_SESSION['engineer']['git_id'] ?? $_SESSION['engineer']['username'] ?? '')));
        $repoOwner = strtolower(trim($owner));
        if ($currentGitUser === '' || $repoOwner === '' || $repoOwner !== $currentGitUser) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => $isZhTw
                    ? ('Repo 擁有者必須是目前登入的 Git 用戶（' . ($currentGitUser !== '' ? $currentGitUser : '-') . '）。')
                    : ('Repository owner must match the current signed-in Git user (' . ($currentGitUser !== '' ? $currentGitUser : '-') . ').'),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }
        $canonicalRepoUrl = 'https://github.com/' . $owner . '/' . $repo;

        $repoRes = neejou_http_get_json('https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo));
        if (($repoRes['status'] ?? 0) >= 400 || !is_array($repoRes['data'] ?? null)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => $isZhTw ? '找不到這個 GitHub 專案，請確認 URL 是否正確。' : 'Repository not found. Please check the URL.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $repoData = (array) $repoRes['data'];
        $repoName = trim((string) ($repoData['name'] ?? $repo));

        $repoLangRes = neejou_http_get_json('https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo) . '/languages');
        if (($repoLangRes['status'] ?? 0) >= 400 || !is_array($repoLangRes['data'] ?? null)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => $isZhTw ? '無法檢查 Repo 語言，請稍後再試。' : 'Cannot inspect repository languages right now.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $detectedLangs = array_keys((array) $repoLangRes['data']);
        $detectedNormSet = [];
        foreach ($detectedLangs as $dl) {
            $k = neejou_normalize_label((string) $dl);
            if ($k !== '') $detectedNormSet[$k] = true;
        }

        $missingLangs = [];
        foreach ($languages as $lang) {
            $k = neejou_normalize_label((string) $lang);
            if ($k === '') continue;
            if (!isset($detectedNormSet[$k])) {
                $missingLangs[] = (string) $lang;
            }
        }

        if (!empty($missingLangs)) {
            http_response_code(400);
            $msg = $isZhTw
                ? ('語言檢查未通過，Repo 內未找到：' . implode(', ', $missingLangs))
                : ('Language verification failed. Not found in repository: ' . implode(', ', $missingLangs));
            echo json_encode([
                'ok' => false,
                'message' => $msg,
                'detected_languages' => $detectedLangs,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $db->orderBy('id', 'ASC');
        $langRows = $db->get('languages', null, ['id', 'display_name', 'code']);
        if (!is_array($langRows)) $langRows = [];
        $langMapRes = neejou_map_labels_to_ids($langRows, $languages);
        if (!empty($langMapRes['missing'])) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => $isZhTw ? '語言字典比對失敗。' : 'Language dictionary mapping failed.',
                'missing' => $langMapRes['missing'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }


        $databaseRows = $db->rawQuery('SELECT id, display_name, code FROM `databases` ORDER BY id ASC');
        if (!is_array($databaseRows)) $databaseRows = [];
        $dbMapRes = neejou_map_labels_to_ids($databaseRows, $databases);
        if (!empty($dbMapRes['missing'])) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => $isZhTw ? '資料庫字典比對失敗。' : 'Database dictionary mapping failed.',
                'missing' => $dbMapRes['missing'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $db->orderBy('id', 'ASC');
        $catRows = $db->get('project_categories', null, ['id', 'display_name', 'code']);
        if (!is_array($catRows)) $catRows = [];
        $catMapRes = neejou_map_labels_to_ids($catRows, $keywords);
        if (!empty($catMapRes['missing'])) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'message' => $isZhTw ? '關鍵字字典比對失敗。' : 'Keyword dictionary mapping failed.',
                'missing' => $catMapRes['missing'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $repoId = 0;
        if ($repoIdInput > 0) {
            $db->where('id', $repoIdInput);
            $db->where('engineer_id', $engineerId);
            $owned = $db->getOne('repo_list', ['id']);
            if (!is_array($owned) || empty($owned['id'])) {
                http_response_code(403);
                echo json_encode(['ok' => false, 'message' => $isZhTw ? '無權限編輯此專案。' : 'No permission to edit this repository.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $db->where('engineer_id', $engineerId);
            $db->where('repo_url', $canonicalRepoUrl);
            $db->where('id', $repoIdInput, '!=');
            $dup = $db->getOne('repo_list', ['id']);
            if (is_array($dup) && !empty($dup['id'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'message' => $isZhTw ? '此 Repo 已存在於你的列表。' : 'This repository already exists in your list.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $repoId = $repoIdInput;
            $db->where('id', $repoId);
            $db->where('engineer_id', $engineerId);
            $db->update('repo_list', [
                'repo_url' => $canonicalRepoUrl,
                'repo_name' => $repoName,
                'last_update_at' => $now,
            ]);
        } else {
            $db->where('engineer_id', $engineerId);
            $db->where('repo_url', $canonicalRepoUrl);
            $existing = $db->getOne('repo_list', ['id']);

            if (is_array($existing) && !empty($existing['id'])) {
                $repoId = (int) $existing['id'];
                $db->where('id', $repoId);
                $db->where('engineer_id', $engineerId);
                $db->update('repo_list', [
                    'repo_name' => $repoName,
                    'last_update_at' => $now,
                ]);
            } else {
                $repoId = (int) $db->insert('repo_list', [
                    'engineer_id' => $engineerId,
                    'repo_url' => $canonicalRepoUrl,
                    'repo_name' => $repoName,
                    'created_at' => $now,
                    'last_update_at' => $now,
                ]);
            }
        }

        if ($repoId <= 0) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => $isZhTw ? '儲存失敗，請稍後再試。' : 'Save failed. Please try again later.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('repo_id', $repoId);
        $db->delete('repo_languages');
        foreach ($langMapRes['ids'] as $langId) {
            $db->insert('repo_languages', [
                'repo_id' => $repoId,
                'language_id' => (int) $langId,
                'created_at' => $now,
            ]);
        }


        $db->where('repo_id', $repoId);
        $db->delete('repo_databases');
        foreach ($dbMapRes['ids'] as $databaseId) {
            $db->insert('repo_databases', [
                'repo_id' => $repoId,
                'database_id' => (int) $databaseId,
                'created_at' => $now,
            ]);
        }

        $db->where('repo_id', $repoId);
        $db->delete('repo_project_categories');
        foreach ($catMapRes['ids'] as $catId) {
            $db->insert('repo_project_categories', [
                'repo_id' => $repoId,
                'category_id' => (int) $catId,
                'created_at' => $now,
            ]);
        }

        echo json_encode([
            'ok' => true,
            'repo_id' => $repoId,
            'repo_name' => $repoName,
            'message' => $isZhTw ? '已儲存 Git 專案。' : 'Git project saved.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if ($action === 'get_git_repo_detail') {
        header('Content-Type: application/json; charset=UTF-8');
        $repoId = (int) ($_POST['repo_id'] ?? 0);
        if ($repoId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_repo_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('id', $repoId);
        $db->where('engineer_id', $engineerId);
        $repoRow = $db->getOne('repo_list', ['id', 'repo_url', 'repo_name']);
        if (!is_array($repoRow) || empty($repoRow['id'])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'not_found'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $langRows = $db->rawQuery(
            'SELECT l.display_name, l.code FROM repo_languages rl JOIN languages l ON l.id = rl.language_id WHERE rl.repo_id = ? ORDER BY l.display_name ASC',
            [$repoId]
        );
        if (!is_array($langRows)) $langRows = [];
        $languagesOut = [];
        foreach ($langRows as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') $label = trim((string) ($row['code'] ?? ''));
            if ($label !== '') $languagesOut[] = $label;
        }


        $dbRows2 = $db->rawQuery(
            'SELECT d.display_name, d.code FROM repo_databases rd JOIN `databases` d ON d.id = rd.database_id WHERE rd.repo_id = ? ORDER BY d.display_name ASC',
            [$repoId]
        );
        if (!is_array($dbRows2)) $dbRows2 = [];
        $databasesOut = [];
        foreach ($dbRows2 as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') $label = trim((string) ($row['code'] ?? ''));
            if ($label !== '') $databasesOut[] = $label;
        }

        $catRows2 = $db->rawQuery(
            'SELECT pc.display_name, pc.code FROM repo_project_categories rpc JOIN project_categories pc ON pc.id = rpc.category_id WHERE rpc.repo_id = ? ORDER BY pc.display_name ASC',
            [$repoId]
        );
        if (!is_array($catRows2)) $catRows2 = [];
        $keywordsOut = [];
        foreach ($catRows2 as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') $label = trim((string) ($row['code'] ?? ''));
            if ($label !== '') $keywordsOut[] = $label;
        }

        echo json_encode([
            'ok' => true,
            'repo' => [
                'id' => (int) $repoRow['id'],
                'repo_url' => (string) ($repoRow['repo_url'] ?? ''),
                'repo_name' => (string) ($repoRow['repo_name'] ?? ''),
                'languages' => $languagesOut,
                'databases' => $databasesOut,
                'keywords' => $keywordsOut,
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'delete_git_repo') {
        header('Content-Type: application/json; charset=UTF-8');
        $repoId = (int) ($_POST['repo_id'] ?? 0);
        if ($repoId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_repo_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('id', $repoId);
        $db->where('engineer_id', $engineerId);
        $deleted = $db->delete('repo_list', 1);

        echo json_encode(['ok' => (bool) $deleted], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}


$languageOptions = [];
$databaseOptions = [];
$keywordOptions = [];

try {
    $db->orderBy('display_name', 'ASC');
    $langRows = $db->get('languages', null, ['display_name', 'code']);
    if (is_array($langRows)) {
        foreach ($langRows as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') {
                $label = trim((string) ($row['code'] ?? ''));
            }
            if ($label !== '') {
                $languageOptions[] = $label;
            }
        }
    }
} catch (Throwable $e) {
    $languageOptions = [];
}


try {
    $dbRows = $db->rawQuery('SELECT display_name, code FROM `databases` ORDER BY display_name ASC');
    if (is_array($dbRows)) {
        foreach ($dbRows as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') {
                $label = trim((string) ($row['code'] ?? ''));
            }
            if ($label !== '') {
                $databaseOptions[] = $label;
            }
        }
    }
} catch (Throwable $e) {
    $databaseOptions = [];
}

try {
    $db->orderBy('display_name', 'ASC');
    $catRows = $db->get('project_categories', null, ['display_name', 'code']);
    if (is_array($catRows)) {
        foreach ($catRows as $row) {
            $label = trim((string) ($row['display_name'] ?? ''));
            if ($label === '') {
                $label = trim((string) ($row['code'] ?? ''));
            }
            if ($label !== '') {
                $keywordOptions[] = $label;
            }
        }
    }
} catch (Throwable $e) {
    $keywordOptions = [];
}

$myRepoProjects = [];
try {
    $myRepoProjects = $db->rawQuery(
        'SELECT rl.id, rl.repo_name, rl.repo_url, '
        . 'GROUP_CONCAT(DISTINCT l.display_name ORDER BY l.display_name SEPARATOR ", ") AS languages, '
        . 'GROUP_CONCAT(DISTINCT d.display_name ORDER BY d.display_name SEPARATOR ", ") AS db_names, '
        . 'GROUP_CONCAT(DISTINCT pc.display_name ORDER BY pc.display_name SEPARATOR ", ") AS keywords '
        . 'FROM repo_list rl '
        . 'LEFT JOIN repo_languages rlg ON rlg.repo_id = rl.id '
        . 'LEFT JOIN languages l ON l.id = rlg.language_id '
        . 'LEFT JOIN repo_databases rd ON rd.repo_id = rl.id '
        . 'LEFT JOIN `databases` d ON d.id = rd.database_id '
        . 'LEFT JOIN repo_project_categories rpc ON rpc.repo_id = rl.id '
        . 'LEFT JOIN project_categories pc ON pc.id = rpc.category_id '
        . 'WHERE rl.engineer_id = ? '
        . 'GROUP BY rl.id, rl.repo_name, rl.repo_url '
        . 'ORDER BY rl.last_update_at DESC',
        [$engineerId]
    );
    if (!is_array($myRepoProjects)) {
        $myRepoProjects = [];
    }
} catch (Throwable $e) {
    $myRepoProjects = [];
}

$userName = trim((string) ($_SESSION['engineer']['name'] ?? $_SESSION['engineer']['username'] ?? $_SESSION['engineer']['email'] ?? $t['unnamed_engineer']));
$userAvatar = trim((string) ($_SESSION['engineer']['avatar_url'] ?? ''));
$userInitial = strtoupper(substr($userName !== '' ? $userName : 'E', 0, 1));

$view = (string) ($_GET['view'] ?? 'dashboard');
if (!in_array($view, ['dashboard', 'add-git', 'my-projects'], true)) {
    $view = 'dashboard';
}
?>
<!doctype html>
<html lang="<?= htmlspecialchars($t['lang'], ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>neeJou - Engineer Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .hide-scrollbar{ -ms-overflow-style:none; scrollbar-width:none; }
    .hide-scrollbar::-webkit-scrollbar{ width:0 !important; height:0 !important; background:transparent; }
  </style>
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
  <div id="panel-backdrop" class="fixed inset-0 z-30 hidden bg-black/45 md:hidden"></div>

  <aside id="left-panel" class="fixed left-0 top-0 z-40 h-screen w-[260px] -translate-x-full border-r border-zinc-200 bg-zinc-100/95 p-3 transition-all duration-300 dark:border-zinc-800 dark:bg-zinc-900/95 md:translate-x-0 md:p-4">
    <div class="mb-4 flex items-center">
      <button id="panel-toggle" type="button" class="hidden rounded-lg p-2 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 md:inline-flex" aria-label="Toggle panel">
        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
    </div>

    <nav class="space-y-1">
      <a href="./engineer_dashboard.php?view=dashboard" class="menu-item inline-flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-sm transition <?= $view === 'dashboard' ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200' ?>">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z"/></svg>
        <span class="menu-label truncate"><?= htmlspecialchars($t['dashboard'], ENT_QUOTES, 'UTF-8') ?></span>
      </a>

      <button id="open-add-git-modal" type="button" class="menu-item inline-flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-left text-sm text-zinc-500 transition hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        <span class="menu-label truncate"><?= htmlspecialchars($t['add_git_project'], ENT_QUOTES, 'UTF-8') ?></span>
      </button>

      <a href="./engineer_dashboard.php?view=my-projects" class="menu-item inline-flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-sm transition <?= $view === 'my-projects' ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200' ?>">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
        <span class="menu-label truncate"><?= htmlspecialchars($t['my_projects'], ENT_QUOTES, 'UTF-8') ?></span>
      </a>
    </nav>

    <div class="my-3 border-t border-zinc-200 dark:border-zinc-800"></div>
  </aside>

  <main id="content" class="min-h-screen transition-all duration-300 md:ml-[260px]">
    <header class="sticky top-0 z-20 bg-white/85 px-4 py-3 backdrop-blur dark:bg-zinc-950/85 sm:px-6">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <button id="mobile-panel-toggle" type="button" class="rounded-lg p-2 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100 md:hidden" aria-label="Open panel">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          <img src="./images/logo.webp" alt="neeJou Logo" class="h-10 w-auto object-contain" />
        </div>

        <div class="min-w-0 flex-1 px-2 text-center">
          <p class="mx-auto max-w-full truncate text-base font-medium text-zinc-600 dark:text-zinc-300"><?= htmlspecialchars($view === 'my-projects' ? $t['my_projects'] : $t['title'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div class="relative shrink-0">
          <button id="user-menu-btn" type="button" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 bg-white px-2 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-zinc-800">
            <?php if ($userAvatar !== ''): ?>
              <img src="<?= htmlspecialchars($userAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" class="h-8 w-8 rounded-full object-cover" />
            <?php else: ?>
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-zinc-200 text-xs font-semibold text-zinc-700 dark:bg-zinc-700 dark:text-zinc-100"><?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <span class="hidden max-w-[120px] truncate sm:inline"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
          </button>

          <div id="user-menu" class="absolute right-0 mt-2 hidden min-w-[170px] rounded-xl border border-zinc-200 bg-white p-1 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <a href="./engineer_profile.php" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['profile'], ENT_QUOTES, 'UTF-8') ?></a>
            <a href="./logout.php" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['logout'], ENT_QUOTES, 'UTF-8') ?></a>
          </div>
        </div>
      </div>
    </header>

    <section class="p-4 sm:p-6">
      <?php if ($view === 'my-projects'): ?>
        <div class="rounded-2xl border border-zinc-200 bg-white/80 p-5 dark:border-zinc-800 dark:bg-zinc-900/60 sm:p-7">
          <h2 class="text-2xl font-semibold tracking-tight"><?= htmlspecialchars($t['my_projects'], ENT_QUOTES, 'UTF-8') ?></h2>
          <?php if (empty($myRepoProjects)): ?>
            <p class="mt-3 text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars($t['no_repo_projects'], ENT_QUOTES, 'UTF-8') ?></p>
          <?php else: ?>
            <div class="mt-4 overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="border-b border-zinc-200 text-left text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    <th class="px-3 py-2 font-medium"><?= htmlspecialchars($t['project_name_col'], ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="px-3 py-2 font-medium"><?= htmlspecialchars($t['languages_col'], ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="px-3 py-2 font-medium"><?= htmlspecialchars($t['databases_col'], ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="px-3 py-2 font-medium"><?= htmlspecialchars($t['keywords_col'], ENT_QUOTES, 'UTF-8') ?></th>
                    <th class="px-3 py-2 text-right font-medium"><?= htmlspecialchars($t['actions_col'], ENT_QUOTES, 'UTF-8') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($myRepoProjects as $proj): ?>
                    <tr class="border-b border-zinc-100 align-top dark:border-zinc-800/70">
                      <td class="px-3 py-3">
                        <p class="font-medium"><?= htmlspecialchars((string) ($proj['repo_name'] ?: 'Untitled Repo'), ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars((string) ($proj['repo_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                      </td>
                      <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars((string) ($proj['languages'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars((string) ($proj['db_names'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="px-3 py-3 text-zinc-700 dark:text-zinc-300"><?= htmlspecialchars((string) ($proj['keywords'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="px-3 py-3">
                        <div class="flex justify-end gap-2">
                          <button type="button" class="edit-repo-btn rounded-lg border border-zinc-300 px-3 py-1.5 text-xs hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800" data-repo-id="<?= (int) ($proj['id'] ?? 0) ?>"><?= htmlspecialchars($t['edit'], ENT_QUOTES, 'UTF-8') ?></button>
                          <button type="button" class="delete-repo-btn rounded-lg border border-red-300 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40" data-repo-id="<?= (int) ($proj['id'] ?? 0) ?>"><?= htmlspecialchars($t['delete'], ENT_QUOTES, 'UTF-8') ?></button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <?= $aboutHtml ?>
      <?php endif; ?>
    </section>
  </main>

  <div id="git-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-3xl rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 sm:p-7">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold"><?= htmlspecialchars($t['modal_title'], ENT_QUOTES, 'UTF-8') ?></h3>
        <button id="close-git-modal" type="button" class="rounded-lg px-2 py-1 text-sm text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['close'], ENT_QUOTES, 'UTF-8') ?></button>
      </div>

      <form id="git-form" class="mt-5 space-y-5">
        <div>
          <label class="mb-2 block text-sm font-medium"><?= htmlspecialchars($t['repo_url'], ENT_QUOTES, 'UTF-8') ?></label>
          <input id="repo-url-input" type="url" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" placeholder="<?= htmlspecialchars($t['repo_url_placeholder'], ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium"><?= htmlspecialchars($t['languages'], ENT_QUOTES, 'UTF-8') ?></label>
          <div id="languages-selector" class="rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900"></div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium"><?= htmlspecialchars($t['databases'], ENT_QUOTES, 'UTF-8') ?></label>
          <div id="databases-selector" class="rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900"></div>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium"><?= htmlspecialchars($t['keywords'], ENT_QUOTES, 'UTF-8') ?></label>
          <div id="keywords-selector" class="rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-900"></div>
        </div>

        <p class="text-xs text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($t['save_hint'], ENT_QUOTES, 'UTF-8') ?></p>

        <p id="git-form-error" class="hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300"></p>

        <div class="flex justify-end gap-2 pt-2">
          <button id="cancel-git-modal" type="button" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['close'], ENT_QUOTES, 'UTF-8') ?></button>
          <button id="git-submit-btn" type="submit" class="inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm font-medium hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-70 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800">
            <svg id="git-submit-spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg>
            <span id="git-submit-label"><?= htmlspecialchars($t['done'], ENT_QUOTES, 'UTF-8') ?></span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      var panel = document.getElementById('left-panel');
      var content = document.getElementById('content');
      var toggle = document.getElementById('panel-toggle');
      var mobileToggle = document.getElementById('mobile-panel-toggle');
      var backdrop = document.getElementById('panel-backdrop');
      var userBtn = document.getElementById('user-menu-btn');
      var userMenu = document.getElementById('user-menu');

      function isDesktop() { return window.matchMedia('(min-width: 768px)').matches; }
      function closeMobilePanel() {
        panel.classList.add('-translate-x-full');
        panel.classList.remove('translate-x-0');
        backdrop.classList.add('hidden');
      }
      function openMobilePanel() {
        panel.classList.remove('-translate-x-full');
        panel.classList.add('translate-x-0');
        backdrop.classList.remove('hidden');
      }

      mobileToggle && mobileToggle.addEventListener('click', function () { if (!isDesktop()) openMobilePanel(); });
      backdrop && backdrop.addEventListener('click', closeMobilePanel);
      document.querySelectorAll('#left-panel a').forEach(function (a) {
        a.addEventListener('click', function () { if (!isDesktop()) closeMobilePanel(); });
      });

      if (toggle) {
        var collapsed = false;
        toggle.addEventListener('click', function () {
          if (!isDesktop()) return;
          collapsed = !collapsed;
          if (collapsed) {
            panel.classList.remove('w-[260px]');
            panel.classList.add('w-[78px]');
            content.classList.remove('md:ml-[260px]');
            content.classList.add('md:ml-[78px]');
            document.querySelectorAll('.menu-label').forEach(function (el) { el.classList.add('hidden'); });
            document.querySelectorAll('.menu-item').forEach(function (el) { el.classList.remove('gap-3'); el.classList.add('justify-center'); });
          } else {
            panel.classList.remove('w-[78px]');
            panel.classList.add('w-[260px]');
            content.classList.remove('md:ml-[78px]');
            content.classList.add('md:ml-[260px]');
            document.querySelectorAll('.menu-label').forEach(function (el) { el.classList.remove('hidden'); });
            document.querySelectorAll('.menu-item').forEach(function (el) { el.classList.add('gap-3'); el.classList.remove('justify-center'); });
          }
        });
      }

      window.addEventListener('resize', function () {
        if (isDesktop()) {
          backdrop.classList.add('hidden');
          panel.classList.remove('-translate-x-full');
          panel.classList.add('translate-x-0');
        } else {
          closeMobilePanel();
        }
      });

      if (userBtn && userMenu) {
        userBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          userMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function () { userMenu.classList.add('hidden'); });
      }

      var gitModal = document.getElementById('git-modal');
      var openGitBtn = document.getElementById('open-add-git-modal');
      var closeGitBtn = document.getElementById('close-git-modal');
      var cancelGitBtn = document.getElementById('cancel-git-modal');
      var gitForm = document.getElementById('git-form');
      var repoUrlInput = document.getElementById('repo-url-input');
      var gitFormError = document.getElementById('git-form-error');
      var gitSubmitBtn = document.getElementById('git-submit-btn');
      var gitSubmitSpinner = document.getElementById('git-submit-spinner');
      var gitSubmitLabel = document.getElementById('git-submit-label');
      var gitSubmitDefault = '<?= htmlspecialchars($t['done'], ENT_QUOTES, 'UTF-8') ?>';
      var gitSubmitRunning = '<?= htmlspecialchars($t['submitting'], ENT_QUOTES, 'UTF-8') ?>';
      var currentEditingRepoId = 0;
      var currentView = '<?= htmlspecialchars($view, ENT_QUOTES, 'UTF-8') ?>';

      function openGitModal() {
        if (gitFormError) {
          gitFormError.classList.add('hidden');
          gitFormError.textContent = '';
        }
        gitModal.classList.remove('hidden');
        gitModal.classList.add('flex');
        setTimeout(function () { repoUrlInput && repoUrlInput.focus(); }, 20);
      }
      function closeGitModal() {
        gitModal.classList.add('hidden');
        gitModal.classList.remove('flex');
      }

      function setGitSubmitting(isRunning) {
        if (!gitSubmitBtn || !gitSubmitSpinner || !gitSubmitLabel) return;
        gitSubmitBtn.disabled = !!isRunning;
        gitSubmitSpinner.classList.toggle('hidden', !isRunning);
        gitSubmitLabel.textContent = isRunning ? gitSubmitRunning : gitSubmitDefault;
        if (cancelGitBtn) cancelGitBtn.disabled = !!isRunning;
        if (closeGitBtn) closeGitBtn.disabled = !!isRunning;
      }

      openGitBtn && openGitBtn.addEventListener('click', function () {
        if (!isDesktop()) closeMobilePanel();
        currentEditingRepoId = 0;
        if (repoUrlInput) repoUrlInput.value = '';
        if (typeof langSelector !== 'undefined' && langSelector && langSelector.reset) langSelector.reset();
        if (typeof databaseSelector !== 'undefined' && databaseSelector && databaseSelector.reset) databaseSelector.reset();
        if (typeof keywordSelector !== 'undefined' && keywordSelector && keywordSelector.reset) keywordSelector.reset();
        openGitModal();
      });
      closeGitBtn && closeGitBtn.addEventListener('click', closeGitModal);
      cancelGitBtn && cancelGitBtn.addEventListener('click', closeGitModal);

      function createTagSelector(rootId, options, placeholder) {
        var root = document.getElementById(rootId);
        if (!root) return { getValues: function () { return []; }, reset: function () {} };

        var selected = [];
        var wrap = document.createElement('div');
        wrap.className = 'relative';

        var chipWrap = document.createElement('div');
        chipWrap.className = 'flex min-h-[44px] flex-wrap items-center gap-2 pr-2';

        var input = document.createElement('input');
        input.type = 'text';
        input.placeholder = placeholder;
        input.className = 'min-w-[180px] flex-1 bg-transparent py-1 text-sm outline-none placeholder:text-zinc-500 dark:placeholder:text-zinc-400';

        var dropdown = document.createElement('div');
        dropdown.className = 'hide-scrollbar absolute left-0 right-0 top-[calc(100%+8px)] z-20 hidden max-h-52 overflow-y-auto rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900';

        var isOpen = false;

        function normalize(v) { return (v || '').toLowerCase().trim(); }

        function removeChip(v) {
          selected = selected.filter(function (x) { return x !== v; });
          render();
        }

        function addValue(v) {
          var raw = (v || '').trim();
          if (!raw) return;
          var found = options.find(function (opt) { return normalize(opt) === normalize(raw); });
          var val = found || raw;
          if (selected.some(function (x) { return normalize(x) === normalize(val); })) return;
          selected.push(val);
          input.value = '';
          render();
        }

        function renderDropdown() {
          if (!isOpen) {
            dropdown.classList.add('hidden');
            return;
          }

          var q = normalize(input.value);
          dropdown.innerHTML = '';

          var candidates = options.filter(function (opt) {
            if (selected.some(function (x) { return normalize(x) === normalize(opt); })) return false;
            if (!q) return true;
            return normalize(opt).indexOf(q) !== -1;
          }).slice(0, 50);

          if (!candidates.length) {
            dropdown.classList.add('hidden');
            return;
          }

          candidates.forEach(function (item) {
            var row = document.createElement('button');
            row.type = 'button';
            row.className = 'block w-full px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800';
            row.textContent = item;
            row.addEventListener('mousedown', function (e) {
              e.preventDefault();
              addValue(item);
            });
            dropdown.appendChild(row);
          });
          dropdown.classList.remove('hidden');
        }

        function render() {
          chipWrap.innerHTML = '';
          selected.forEach(function (item) {
            var chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 rounded bg-zinc-200 px-2 py-1 text-sm text-zinc-800 dark:bg-zinc-700 dark:text-zinc-100';
            var txt = document.createElement('span');
            txt.textContent = item;
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100';
            rm.textContent = '×';
            rm.addEventListener('click', function () { removeChip(item); });
            chip.appendChild(txt);
            chip.appendChild(rm);
            chipWrap.appendChild(chip);
          });
          chipWrap.appendChild(input);
          renderDropdown();
        }

        input.addEventListener('focus', function () {
          isOpen = true;
          renderDropdown();
        });
        input.addEventListener('input', function () {
          isOpen = true;
          renderDropdown();
        });
        input.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addValue(input.value);
          } else if (e.key === 'Backspace' && input.value === '' && selected.length > 0) {
            selected.pop();
            render();
          }
        });
        input.addEventListener('blur', function () {
          isOpen = false;
          setTimeout(function () { dropdown.classList.add('hidden'); }, 120);
        });

        wrap.appendChild(chipWrap);
        wrap.appendChild(dropdown);
        root.appendChild(wrap);
        render();

        return {
          getValues: function () { return selected.slice(); },
          reset: function () {
            selected = [];
            input.value = '';
            render();
          },
          setValues: function (vals) {
            if (!Array.isArray(vals)) vals = [];
            selected = [];
            vals.forEach(function (v) {
              var raw = String(v || '').trim();
              if (!raw) return;
              var found = options.find(function (opt) { return normalize(opt) === normalize(raw); });
              var val = found || raw;
              if (selected.some(function (x) { return normalize(x) === normalize(val); })) return;
              selected.push(val);
            });
            input.value = '';
            render();
          }
        };
      }

      var languageOptions = <?= json_encode($languageOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        .slice()
        .sort(function (a, b) { return String(a).localeCompare(String(b), undefined, { sensitivity: 'base' }); });

      var databaseOptions = <?= json_encode($databaseOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        .slice()
        .sort(function (a, b) { return String(a).localeCompare(String(b), undefined, { sensitivity: 'base' }); });

      var keywordOptions = <?= json_encode($keywordOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        .slice()
        .sort(function (a, b) { return String(a).localeCompare(String(b), undefined, { sensitivity: 'base' }); });

      var langSelector = createTagSelector('languages-selector', languageOptions, '<?= htmlspecialchars($t['search_lang_placeholder'], ENT_QUOTES, 'UTF-8') ?>');
      var databaseSelector = createTagSelector('databases-selector', databaseOptions, '<?= htmlspecialchars($t['search_db_placeholder'], ENT_QUOTES, 'UTF-8') ?>');
      var keywordSelector = createTagSelector('keywords-selector', keywordOptions, '<?= htmlspecialchars($t['search_keyword_placeholder'], ENT_QUOTES, 'UTF-8') ?>');


      document.querySelectorAll('.edit-repo-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var repoId = Number(btn.getAttribute('data-repo-id') || 0);
          if (!repoId) return;

          if (gitFormError) {
            gitFormError.classList.add('hidden');
            gitFormError.textContent = '';
          }

          fetch('./engineer_dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'get_git_repo_detail', repo_id: repoId })
          })
            .then(function (res) {
              return res.json().then(function (data) {
                if (!res.ok || !data || !data.ok || !data.repo) {
                  throw new Error('<?= htmlspecialchars($t['load_repo_failed'], ENT_QUOTES, 'UTF-8') ?>');
                }
                return data.repo;
              });
            })
            .then(function (repo) {
              currentEditingRepoId = Number(repo.id || repoId);
              if (repoUrlInput) repoUrlInput.value = String(repo.repo_url || '');
              langSelector.setValues(Array.isArray(repo.languages) ? repo.languages : []);
              databaseSelector.setValues(Array.isArray(repo.databases) ? repo.databases : []);
              keywordSelector.setValues(Array.isArray(repo.keywords) ? repo.keywords : []);
              openGitModal();
            })
            .catch(function (err) {
              alert(err && err.message ? err.message : '<?= htmlspecialchars($t['load_repo_failed'], ENT_QUOTES, 'UTF-8') ?>');
            });
        });
      });

      document.querySelectorAll('.delete-repo-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var repoId = Number(btn.getAttribute('data-repo-id') || 0);
          if (!repoId) return;
          if (!confirm('<?= htmlspecialchars($t['confirm_delete_repo'], ENT_QUOTES, 'UTF-8') ?>')) return;

          fetch('./engineer_dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete_git_repo', repo_id: repoId })
          })
            .then(function (res) {
              return res.json().then(function (data) {
                if (!res.ok || !data || !data.ok) {
                  throw new Error('Request failed');
                }
                return data;
              });
            })
            .then(function () {
              window.location.reload();
            })
            .catch(function () {
              alert('Request failed');
            });
        });
      });


      gitForm && gitForm.addEventListener('submit', function (e) {
        e.preventDefault();

        if (gitFormError) {
          gitFormError.classList.add('hidden');
          gitFormError.textContent = '';
        }

        var payload = {
          action: 'add_git_repo',
          repo_id: currentEditingRepoId || 0,
          repo_url: (repoUrlInput && repoUrlInput.value || '').trim(),
          languages: langSelector.getValues(),
          databases: databaseSelector.getValues(),
          keywords: keywordSelector.getValues()
        };

        if (!payload.repo_url) {
          if (gitFormError) {
            gitFormError.textContent = '<?= htmlspecialchars($t['validation_repo_url'], ENT_QUOTES, 'UTF-8') ?>';
            gitFormError.classList.remove('hidden');
          }
          return;
        }
        if (!payload.languages.length) {
          if (gitFormError) {
            gitFormError.textContent = '<?= htmlspecialchars($t['validation_languages'], ENT_QUOTES, 'UTF-8') ?>';
            gitFormError.classList.remove('hidden');
          }
          return;
        }

        setGitSubmitting(true);

        fetch('./engineer_dashboard.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
          .then(function (res) {
            return res.json().then(function (data) {
              if (!res.ok || !data || !data.ok) {
                var msg = (data && data.message) ? String(data.message) : 'Request failed';
                throw new Error(msg);
              }
              return data;
            });
          })
          .then(function (data) {
            alert(data.message || 'Saved');
            if (repoUrlInput) repoUrlInput.value = '';
            langSelector.reset();
            databaseSelector.reset();
            keywordSelector.reset();
            currentEditingRepoId = 0;
            closeGitModal();
            if (currentView === 'my-projects') {
              window.location.reload();
            }
          })
          .catch(function (err) {
            if (gitFormError) {
              gitFormError.textContent = err && err.message ? err.message : 'Request failed';
              gitFormError.classList.remove('hidden');
            }
          })
          .finally(function () {
            setGitSubmitting(false);
          });
      });
    })();

  </script>
</body>
</html>
