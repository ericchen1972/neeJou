<?php
session_start();
require_once __DIR__ . '/MysqliDb.php';
require_once __DIR__ . '/mysql.php';

if (empty($_SESSION['client']['id'])) {
    header('Location: ./index.php');
    exit;
}

$acceptLanguage = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
$isZhTw = strpos($acceptLanguage, 'zh-tw') !== false || strpos($acceptLanguage, 'zh-hant') !== false;

$t = $isZhTw
    ? [
        'lang' => 'zh-Hant',
        'dashboard' => '關於 neeJou',
        'create_project' => '建立專案',
        'dashboard_title' => '儀表板',
        'dashboard_note' => '這裡是業主總覽頁，之後可放專案進度、媒合狀態與通知。',
        'welcome' => '歡迎回來，',
        'profile' => '個人資料',
        'logout' => '登出',
        'ask_placeholder' => '問問 neeJou',
        'ask_placeholder_running' => '執行中...',
        'send' => '送出',
        'copy' => 'Copy',
        'copied' => '已複製',
        'project_list' => '專案列表',
        'rename' => '重新命名',
        'delete' => '刪除',
        'no_projects' => '尚無專案',
        'confirm_delete_project' => '確定要刪除這個專案嗎？',
        'summary_title' => '專案摘要',
        'summary_project_name' => '專案名稱',
        'summary_stacks' => '使用語言與資料庫',
        'summary_languages' => '使用語言',
        'summary_database' => '資料庫',
        'summary_mode' => '專案型態',
        'summary_types' => '專案類型',
        'summary_budget' => '預算',
        'summary_foreign' => '接受跨國工程師',
        'summary_ai_description' => 'AI彙整需求',
        'step' => '步驟',
        'close' => '關閉',
        'next' => '下一步',
        'prev' => '上一步',
        'submit' => '建立專案',
        'created' => '專案需求已建立（Demo）。',
        'validation_mode' => '請先選擇專案型態。',
        'validation_type' => '請至少選擇一個專案類型。',
        'validation_stack' => '請至少選擇一個語言或資料庫。',
        'validation_project_name' => '請輸入專案名稱（至少 3 個字元）。',
        'validation_budget' => '請輸入有效預算（數字且大於 0）。',
        'validation_foreign' => '請選擇是否接受其他國家工程師。',
        'q1' => '這是一個全新的開發，還是現有專案的維護或修改？',
        'new_dev' => '全新開發',
        'existing' => '現有專案',
        'q2_type' => '這個專案屬於',
        'q2_stack' => '這個專案所使用的語言及資料庫',
        'q3_project_name' => '專案名稱',
        'q3_budget' => '預算',
        'q3_foreign' => '是否接受其他國家工程師',
        'yes' => '是',
        'no' => '否',
        'unnamed_project' => '未命名專案',
        'project_name_placeholder' => '例如：跨境電商平台',
        'budget_placeholder' => '例如 300000',
        'step3_note' => '目前專案的摘要只是粗略的範圍，AI會根據你的需求來修改專案摘要，以便幫你找到最合適的工程師。',
        'project_type_options' => [
            ['label' => '電商', 'value' => 'ecommerce'],
            ['label' => '交友', 'value' => 'dating_platform'],
            ['label' => 'POS', 'value' => 'pos_system'],
            ['label' => '會計', 'value' => 'erp_system'],
            ['label' => '教育', 'value' => 'education_platform'],
            ['label' => '其他', 'value' => 'other'],
        ],
        'stacks' => ['PHP', 'JavaScript', 'Python', 'Java', 'MySQL', 'PostgreSQL', 'MongoDB', '我不清楚'],
      ]
    : [
        'lang' => 'en',
        'dashboard' => 'About neeJou',
        'create_project' => 'Create Project',
        'dashboard_title' => 'Dashboard',
        'dashboard_note' => 'This is the client overview. You can later place project progress, matching status, and notifications here.',
        'welcome' => 'Welcome back, ',
        'profile' => 'Profile',
        'logout' => 'Logout',
        'ask_placeholder' => 'Ask neeJou',
        'ask_placeholder_running' => 'Running...',
        'send' => 'Send',
        'copy' => 'Copy',
        'copied' => 'Copied',
        'project_list' => 'Projects',
        'rename' => 'Rename',
        'delete' => 'Delete',
        'no_projects' => 'No projects yet',
        'confirm_delete_project' => 'Delete this project?',
        'summary_title' => 'Project Summary',
        'summary_project_name' => 'Project Name',
        'summary_stacks' => 'Languages & Databases',
        'summary_languages' => 'Languages',
        'summary_database' => 'Database',
        'summary_mode' => 'Mode',
        'summary_types' => 'Types',
        'summary_budget' => 'Budget',
        'summary_foreign' => 'Overseas Engineers',
        'summary_ai_description' => 'AI Requirement Notes',
        'step' => 'Step',
        'close' => 'Close',
        'next' => 'Next',
        'prev' => 'Back',
        'submit' => 'Create Project',
        'created' => 'Project requirement created (Demo).',
        'validation_mode' => 'Please select the project mode.',
        'validation_type' => 'Please select at least one project type.',
        'validation_stack' => 'Please select at least one language or database.',
        'validation_project_name' => 'Please enter a project name (at least 3 characters).',
        'validation_budget' => 'Please enter a valid budget (number > 0).',
        'validation_foreign' => 'Please choose whether overseas engineers are accepted.',
        'q1' => 'Is this a brand new project or maintenance/modification of an existing one?',
        'new_dev' => 'New Development',
        'existing' => 'Existing Project',
        'q2_type' => 'This project belongs to',
        'q2_stack' => 'Languages and databases used in this project',
        'q3_project_name' => 'Project Name',
        'q3_budget' => 'Budget',
        'q3_foreign' => 'Accept engineers from other countries?',
        'yes' => 'Yes',
        'no' => 'No',
        'unnamed_project' => 'Untitled Project',
        'project_name_placeholder' => 'e.g. Cross-border commerce platform',
        'budget_placeholder' => 'e.g. 300000',
        'step3_note' => 'The current project summary is only a rough scope. AI will update the summary based on your requirements so we can match the most suitable engineer.',
        'project_type_options' => [
            ['label' => 'E-commerce', 'value' => 'ecommerce'],
            ['label' => 'Dating', 'value' => 'dating_platform'],
            ['label' => 'POS', 'value' => 'pos_system'],
            ['label' => 'Accounting', 'value' => 'erp_system'],
            ['label' => 'Education', 'value' => 'education_platform'],
            ['label' => 'Other', 'value' => 'other'],
        ],
        'stacks' => ['PHP', 'JavaScript', 'Python', 'Java', 'MySQL', 'PostgreSQL', 'MongoDB', 'Not sure'],
      ];

$aboutHtml = $isZhTw
    ? <<<'HTML'
<div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/60 md:grid md:grid-cols-3">
  <div class="hidden min-h-[320px] bg-center bg-cover md:col-span-1 md:block lg:min-h-[420px]" style="background-image:url('./images/neejou1.webp');"></div>
  <div class="p-5 sm:p-7 md:col-span-2">
  <h1 class="text-2xl font-semibold">neeJou</h1>
  <p class="mt-2 text-lg font-medium text-zinc-700 dark:text-zinc-200">讓 AI 幫你找到真正適合的工程師</p>
  <div class="mt-6 space-y-5 text-zinc-700 dark:text-zinc-300">
    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">你遇到的問題，我們都知道</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>履歷寫得很好，但實際做不出來</li>
        <li>外包報價漂亮，最後專案爛尾</li>
        <li>技術說得頭頭是道，GitHub 空空如也</li>
        <li>需求講半天，對方其實沒聽懂</li>
      </ul>
      <p class="mt-2">你不缺工程師。你缺的是真正適合你專案的人。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">neeJou 怎麼做？</h2>
      <p class="mt-2">你只要用自然語言描述專案，AI 會幫你整理需求、補齊邏輯、穩定架構。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">我們不是看履歷，我們看 Git 專案</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>分析目錄結構與檔案命名</li>
        <li>參考 README 內容</li>
        <li>評估 commit 活躍度</li>
        <li>比對專案功能與技術棧</li>
      </ul>
      <p class="mt-2">重點不是「會某語言」，而是「真的做過類似系統」。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">幫你過濾不適合的接案者</h2>
      <p class="mt-2">AI 會先篩掉空殼 GitHub、技術不相符、只會說不會做的人，最後給你契合度分數、分析理由與可追溯資料。</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">你只需要做一件事</h2>
      <p class="mt-2 font-medium">告訴 AI：「開始配對」</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">neeJou 的核心理念</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>讓專案成功，不是靠運氣</li>
        <li>讓媒合有依據，不靠話術</li>
        <li>讓技術回歸作品，而不是簡歷</li>
      </ul>
      <p class="mt-2">AI 不是取代工程師，而是幫你找到真正的工程師。</p>
    </section>
  </div>
  </div>
</div>
HTML
    : <<<'HTML'
<div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/60 md:grid md:grid-cols-3">
  <div class="hidden min-h-[320px] bg-center bg-cover md:col-span-1 md:block lg:min-h-[420px]" style="background-image:url('./images/neejou1.webp');"></div>
  <div class="p-5 sm:p-7 md:col-span-2">
  <h1 class="text-2xl font-semibold">neeJou</h1>
  <p class="mt-2 text-lg font-medium text-zinc-700 dark:text-zinc-200">Let AI help you find the right engineer for your project</p>
  <div class="mt-6 space-y-5 text-zinc-700 dark:text-zinc-300">
    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">We know the pain points</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>Strong resumes, weak delivery</li>
        <li>Nice quotes, failed execution</li>
        <li>Confident talk, empty GitHub</li>
        <li>Long discussions, poor understanding</li>
      </ul>
      <p class="mt-2">You are not short of engineers. You are short of the right one.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">How neeJou works</h2>
      <p class="mt-2">Describe your idea in plain language. AI refines requirements, fills logic gaps, and stabilizes project foundations.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">We do not trust resumes. We read repositories.</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>Folder and file structure</li>
        <li>README quality</li>
        <li>Commit activity</li>
        <li>Feature and stack relevance</li>
      </ul>
      <p class="mt-2">It is not about claiming a skill. It is about proven delivery.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Filter low-quality outsourcing risk early</h2>
      <p class="mt-2">AI removes weak-fit candidates first and gives you match score, reasons, and traceable evidence.</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">You only need to do one thing</h2>
      <p class="mt-2 font-medium">Tell AI: "Start matching"</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Core principles</h2>
      <ul class="mt-2 list-disc space-y-1 pl-5">
        <li>Project success should not rely on luck</li>
        <li>Matching should rely on evidence, not sales talk</li>
        <li>Real work should outweigh resume claims</li>
      </ul>
      <p class="mt-2">AI does not replace engineers. It helps you find the real ones.</p>
    </section>
  </div>
  </div>
</div>
HTML;

$clientId = (int) ($_SESSION['client']['id'] ?? 0);

function neejou_load_openai_key(): string
{
    $configPath = __DIR__ . '/config.json';
    if (!is_file($configPath)) {
        return '';
    }

    $raw = file_get_contents($configPath);
    if (!is_string($raw) || $raw === '') {
        return '';
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return '';
    }

    return trim((string) ($data['openai_api_key'] ?? ''));
}


function neejou_normalize_label(string $v): string
{
    $v = strtolower(trim($v));
    return preg_replace('/[^a-z0-9]+/', '', $v) ?? '';
}

function neejou_supported_project_type_ids(): array
{
    return [
        'ecommerce', 'marketplace', 'subscription_platform', 'ticketing_system', 'booking_system',
        'on_demand_service', 'food_delivery', 'ride_hailing', 'pos_system', 'inventory_management',
        'crm_system', 'erp_system', 'cms_system', 'blog_platform', 'news_portal', 'saas_product',
        'b2b_platform', 'b2c_platform', 'c2c_platform', 'affiliate_system', 'membership_system',
        'loyalty_program', 'coupon_system', 'payment_gateway_integration', 'admin_dashboard',
        'data_management_system', 'reporting_dashboard', 'analytics_platform', 'hr_system',
        'attendance_system', 'payroll_system', 'project_management', 'task_management', 'workflow_system',
        'approval_system', 'document_management', 'knowledge_base', 'customer_support_system',
        'helpdesk_system', 'chat_system', 'internal_tool', 'education_platform', 'lms_system',
        'exam_system', 'online_course', 'dating_platform', 'social_network', 'community_forum',
        'event_management', 'healthcare_system', 'clinic_management', 'hospital_system', 'fitness_platform',
        'real_estate_platform', 'property_management', 'fintech_system', 'insurance_system',
        'crypto_platform', 'blockchain_application', 'iot_system', 'smart_device_integration', 'other',
    ];
}

function neejou_project_summary_from_detail(array $detail, string $fallbackName): array
{
    $stacks = $detail['stacks'] ?? [];
    if (!is_array($stacks)) {
        $stacks = [];
    }

    $types = $detail['types'] ?? [];
    if (!is_array($types)) {
        $types = [];
    }

    $dbCandidates = ['mysql', 'postgresql', 'mongodb', 'sqlite', 'mariadb', 'sql server', 'oracle'];
    $languages = [];
    $databases = [];

    foreach ($stacks as $stack) {
        $val = trim((string) $stack);
        if ($val === '') {
            continue;
        }
        $lower = strtolower($val);
        $isDb = false;
        foreach ($dbCandidates as $dbKey) {
            if (strpos($lower, $dbKey) !== false) {
                $isDb = true;
                break;
            }
        }
        if ($isDb) {
            $databases[] = $val;
        } else {
            $languages[] = $val;
        }
    }

    $projectName = trim((string) ($detail['project_name'] ?? ''));
    if ($projectName === '') {
        $projectName = trim($fallbackName);
    }

    $category = '';
    if (!empty($types)) {
        $category = (string) $types[0];
    }

    return [
        'project_name' => $projectName,
        'project_type' => (string) ($detail['mode'] ?? ''),
        'project_category' => $category,
        'languages' => $languages,
        'database' => $databases,
        'budget' => (string) ($detail['budget'] ?? ''),
        'cross_border_engineer' => (string) ($detail['accept_foreign'] ?? ''),
    ];
}

function neejou_build_system_prompt(array $projectSummary, bool $isZhTw): string
{
    $projectJson = json_encode($projectSummary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    $base = <<<'PROMPT'
You are a professional project analysis assistant inside the neeJou platform.

Your responsibility is strictly limited to analyzing and guiding project setup based on the provided project summary JSON.

You MUST strictly follow the rules below.

------------------------------------------------------------
1. Scope Boundary
------------------------------------------------------------

- Only respond to matters directly related to the project.
- Do NOT answer general knowledge questions.
- Do NOT engage in casual conversation.
- If the user asks something unrelated to the project, politely redirect them back to the project topic.
- Never expand beyond the project setup context.

------------------------------------------------------------
0. Two-Phase Operation (IMPORTANT)
------------------------------------------------------------

This assistant operates in two phases:

PHASE A) Project Setup (default)
- Follow all project setup rules in this system prompt.
- Your goal is to refine and stabilize the project summary JSON.

PHASE B) Repository Evaluation (only when explicitly triggered)
- This phase is triggered ONLY when the tool output contains:
  - "phase": "repo_evaluation"
  - and a non-empty "repos" list.
- In PHASE B, these evaluation rules override setup-style questioning behavior.

In PHASE B:
- Evaluate provided repositories for compatibility with the client project.
- Select ONLY ONE best repository.
- Follow evaluation instructions embedded in tool output (for example, "evaluation_prompt").
- Do NOT ask the client additional questions while evaluating repositories.
- Return strict JSON when evaluation mode explicitly requires strict JSON format.

If PHASE B is triggered but repos is empty:
- State that no repository data is available and provide practical next steps.
- Do NOT fabricate repositories.

------------------------------------------------------------
2. Validate Project Summary Completeness
------------------------------------------------------------

The project summary JSON may include:

- project_name
- project_type
- project_category
- languages
- database
- budget
- cross_border_engineer

First, determine whether the information is sufficient to proceed.

Budget handling rule:
- Budget is always in USD in this platform.
- Never ask the client to confirm budget currency.
- Treat `budget` as USD amount directly.

------------------------------------------------------------
3. Special Case: project_category = "other"
------------------------------------------------------------

If project_category equals "other":

Do NOT provide technical advice yet.

Instead, ask:
"Could you describe what kind of project you would like to build?"

Wait for clarification before continuing.


------------------------------------------------------------
4. Missing Programming Language or Database
------------------------------------------------------------

If programming language or database is not specified:

- Do NOT ask vague clarification questions.
- Based on the project category and description, propose a reasonable and practical tech stack.

Respond using this structure:

"You have not specified the programming language or database.
Based on your project type, I recommend:

- Language:
- Database:

Reason:
(Brief, practical explanation.)"

------------------------------------------------------------
5. Technology Mismatch
------------------------------------------------------------

If the selected language or database is clearly unsuitable:

- Do NOT reject the choice abruptly.
- Briefly explain why it may not be ideal.
- Suggest a more appropriate alternative.
- Keep explanations concise and practical.
- Focus on feasibility and maintainability.

------------------------------------------------------------
6. Avoid Over-Detailed Feature Questions
------------------------------------------------------------

Do NOT ask about obvious standard features.

For example:
- E-commerce projects inherently require user accounts, product management, and checkout systems.
- Do NOT ask whether such features are needed.
- Assume standard industry requirements unless explicitly stated otherwise.

------------------------------------------------------------
7. Tone and Style
------------------------------------------------------------

- Professional
- Concise
- Practical
- Focused on feasibility
- No marketing language
- No emotional tone
- No unnecessary elaboration

------------------------------------------------------------

Your goal is to refine and stabilize the project foundation before matching engineers.

Tool usage requirement:
- When the user confirms or changes project summary decisions (such as language, database, budget, cross-border preference, architecture scope, or requirement decisions), you MUST call the update_project_detail tool to update the project summary.
- For project mode, use `project_mode` = `new` or `existing`. For categories, use `project_category` (or `project_types`) with supported type IDs.
- Budget currency is fixed to USD platform-wide; do not ask for USD/TWD/HKD confirmation.
- If the user provides requirement decisions (for example, membership levels), update ai_description via the tool.
- Based on the user's requirements, you MUST select one or more suitable project types and update project summary types via the tool.
- You should actively include additional RELATED project types when they are clearly relevant to the user's scenario (for example, dating + chat should include both dating_platform and chat_system).
- More relevant project types generally improve matching probability, but NEVER add unrelated types.
- If the user's project cannot be mapped to the supported project types list below, reply exactly that this project type is currently not supported.
- When you believe the current project summary is correct and sufficient, ask the client whether they want to start engineer matching now.
- Only when the client explicitly confirms matching (for example: "開始配對", "進行配對", "start matching"), you MUST call the match_engineers tool.
- Do NOT call match_engineers without explicit client confirmation.
- Never claim a tool was called unless it was actually executed. Do NOT output pseudo tool-call text or fake JSON tool snippets.
- If match_engineers returns no matches, clearly state there is no suitable engineer data for the requested categories with the specified language/database, then propose practical alternative language/database options.

Supported project types (use these exact IDs in project summary types):
- ecommerce
- marketplace
- subscription_platform
- ticketing_system
- booking_system
- on_demand_service
- food_delivery
- ride_hailing
- pos_system
- inventory_management
- crm_system
- erp_system
- cms_system
- blog_platform
- news_portal
- saas_product
- b2b_platform
- b2c_platform
- c2c_platform
- affiliate_system
- membership_system
- loyalty_program
- coupon_system
- payment_gateway_integration
- admin_dashboard
- data_management_system
- reporting_dashboard
- analytics_platform
- hr_system
- attendance_system
- payroll_system
- project_management
- task_management
- workflow_system
- approval_system
- document_management
- knowledge_base
- customer_support_system
- helpdesk_system
- chat_system
- internal_tool
- education_platform
- lms_system
- exam_system
- online_course
- dating_platform
- social_network
- community_forum
- event_management
- healthcare_system
- clinic_management
- hospital_system
- fitness_platform
- real_estate_platform
- property_management
- fintech_system
- insurance_system
- crypto_platform
- blockchain_application
- iot_system
- smart_device_integration
- other

PROMPT;

    $base .= "

Project Summary JSON:
" . $projectJson;

    if ($isZhTw) {
        $base .= "

請使用繁體中文回答。\n若無法對應到支援的專案類型，請直接回覆：目前還不支援此類專案。";
    }

    return $base;
}

function neejou_stream_responses_api(string $apiKey, string $systemPrompt, array $messages, callable $onDelta): string
{
    if ($apiKey === '') {
        return '';
    }

    $payload = [
        'model' => 'gpt-5.2',
        'stream' => true,
        'instructions' => $systemPrompt,
        'input' => $messages,
    ];

    $buffer = '';
    $fullText = '';

    $emitDelta = static function (array $event) use (&$fullText, $onDelta): void {
        $delta = '';
        $type = (string) ($event['type'] ?? '');

        if ($type === 'response.output_text.delta' && isset($event['delta']) && is_string($event['delta'])) {
            $delta = $event['delta'];
        } elseif ($type === 'response.output_text' && isset($event['text']) && is_string($event['text'])) {
            $delta = $event['text'];
        } elseif (isset($event['delta']) && is_string($event['delta'])) {
            $delta = $event['delta'];
        }

        if ($delta !== '') {
            $fullText .= $delta;
            $onDelta($delta);
        }
    };

    $ch = curl_init('https://api.openai.com/v1/responses');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 120,
        CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use (&$buffer, $emitDelta): int {
            $buffer .= $data;

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = (string) substr($buffer, $pos + 1);

                if ($line === '' || strpos($line, 'data:') !== 0) {
                    continue;
                }

                $payloadLine = trim(substr($line, 5));
                if ($payloadLine === '' || $payloadLine === '[DONE]') {
                    continue;
                }

                $event = json_decode($payloadLine, true);
                if (is_array($event)) {
                    $emitDelta($event);
                }
            }

            return strlen($data);
        },
    ]);

    curl_exec($ch);
    curl_close($ch);

    return trim($fullText);
}


function neejou_responses_post(string $apiKey, array $payload): array
{
    if ($apiKey === '') {
        return [];
    }

    $ch = curl_init('https://api.openai.com/v1/responses');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 90,
    ]);

    $raw = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!is_string($raw) || $raw === '' || $http < 200 || $http >= 300) {
        return [];
    }

    $res = json_decode($raw, true);
    return is_array($res) ? $res : [];
}

function neejou_extract_response_text(array $response): string
{
    $outputText = trim((string) ($response['output_text'] ?? ''));
    if ($outputText !== '') {
        return $outputText;
    }

    $parts = [];
    $out = $response['output'] ?? [];
    if (!is_array($out)) {
        return '';
    }

    foreach ($out as $item) {
        if (!is_array($item)) {
            continue;
        }
        $content = $item['content'] ?? [];
        if (!is_array($content)) {
            continue;
        }
        foreach ($content as $c) {
            if (!is_array($c)) {
                continue;
            }
            $txt = '';
            if (($c['type'] ?? '') === 'output_text') {
                $txt = (string) ($c['text'] ?? '');
            } elseif (isset($c['text']) && is_string($c['text'])) {
                $txt = $c['text'];
            }
            $txt = trim($txt);
            if ($txt !== '') {
                $parts[] = $txt;
            }
        }
    }

    return trim(implode("

", $parts));
}

function neejou_extract_tool_calls(array $response): array
{
    $calls = [];
    $out = $response['output'] ?? [];
    if (!is_array($out)) {
        return $calls;
    }

    foreach ($out as $item) {
        if (!is_array($item)) {
            continue;
        }
        if (($item['type'] ?? '') !== 'function_call') {
            continue;
        }

        $calls[] = [
            'name' => (string) ($item['name'] ?? ''),
            'arguments' => (string) ($item['arguments'] ?? '{}'),
            'call_id' => (string) ($item['call_id'] ?? ''),
        ];
    }
    return $calls;
}

function neejou_load_project_row(MysqliDb $db, int $clientId, int $projectId): ?array
{
    $db->where('id', $projectId);
    $db->where('client_id', $clientId);
    $row = $db->getOne('projects', ['id', 'project_name', 'project_detail']);
    if (!is_array($row) || empty($row['id'])) {
        return null;
    }
    return $row;
}

function neejou_apply_update_project_detail_tool(MysqliDb $db, int $clientId, int $projectId, array $args, array $t): array
{
    $row = neejou_load_project_row($db, $clientId, $projectId);
    if (!$row) {
        return ['ok' => false, 'message' => 'project_not_found'];
    }

    $detail = [];
    if (!empty($row['project_detail'])) {
        $decoded = json_decode((string) $row['project_detail'], true);
        if (is_array($decoded)) {
            $detail = $decoded;
        }
    }

    $projectName = trim((string) ($row['project_name'] ?? ''));
    $updated = false;

    if (array_key_exists('budget_currency', $detail)) {
        unset($detail['budget_currency']);
        $updated = true;
    }

    $newName = trim((string) ($args['project_name'] ?? ''));
    if ($newName !== '' && $newName !== $projectName) {
        $projectName = $newName;
        $updated = true;
    }

    $modeCandidates = [];
    if (isset($args['project_mode'])) {
        $modeCandidates[] = $args['project_mode'];
    }
    if (isset($args['project_type']) && !is_array($args['project_type'])) {
        $modeCandidates[] = $args['project_type'];
    }
    foreach ($modeCandidates as $modeRaw) {
        $mode = trim((string) $modeRaw);
        if ($mode !== '' && in_array($mode, ['new', 'existing'], true)) {
            if (($detail['mode'] ?? '') !== $mode) {
                $detail['mode'] = $mode;
                $updated = true;
            }
            break;
        }
    }

    $allowedTypeIds = neejou_supported_project_type_ids();
    $categoryPool = [];
    if (isset($args['project_category'])) $categoryPool[] = $args['project_category'];
    if (isset($args['project_types'])) $categoryPool[] = $args['project_types'];
    if (isset($args['types'])) $categoryPool[] = $args['types'];
    if (isset($args['project_type']) && is_array($args['project_type'])) $categoryPool[] = $args['project_type'];

    $nextTypes = [];
    foreach ($categoryPool as $category) {
        if (is_string($category) && trim($category) !== '') {
            $nextTypes[] = trim($category);
            continue;
        }
        if (is_array($category)) {
            foreach ($category as $v) {
                $vv = trim((string) $v);
                if ($vv !== '') $nextTypes[] = $vv;
            }
        }
    }

    if (!empty($nextTypes)) {
        $nextTypes = array_values(array_unique(array_filter($nextTypes, static fn($v) => in_array($v, $allowedTypeIds, true))));
        if (!empty($nextTypes)) {
            $detail['types'] = $nextTypes;
            $updated = true;
        }
    }

    $langs = $args['languages'] ?? null;
    $dbs = $args['database'] ?? null;
    $newStacks = [];
    if (is_array($langs)) {
        foreach ($langs as $v) {
            $vv = trim((string) $v);
            if ($vv !== '') $newStacks[] = $vv;
        }
    }
    if (is_array($dbs)) {
        foreach ($dbs as $v) {
            $vv = trim((string) $v);
            if ($vv !== '') $newStacks[] = $vv;
        }
    }
    if (!empty($newStacks)) {
        $detail['stacks'] = array_values(array_unique($newStacks));
        $updated = true;
    }

    $budget = trim((string) ($args['budget'] ?? ''));
    if ($budget !== '') {
        $detail['budget'] = $budget;
        $updated = true;
    }
    $cross = trim((string) ($args['cross_border_engineer'] ?? ''));
    if (in_array($cross, ['yes', 'no'], true)) {
        $detail['accept_foreign'] = $cross;
        $updated = true;
    }

    $aiReplace = trim((string) ($args['ai_description_replace'] ?? ''));
    $aiAppend = trim((string) ($args['ai_description_append'] ?? ''));
    $currDesc = trim((string) ($detail['ai_description'] ?? ''));
    if ($aiReplace !== '') {
        $detail['ai_description'] = $aiReplace;
        $updated = true;
    } elseif ($aiAppend !== '') {
        $detail['ai_description'] = $currDesc === '' ? $aiAppend : ($currDesc . "\n" . $aiAppend);
        $updated = true;
    }

    if (($detail['project_name'] ?? '') !== $projectName) {
        $detail['project_name'] = $projectName;
        $updated = true;
    }

    if (!$updated) {
        return [
            'ok' => true,
            'updated' => false,
            'project_name' => $projectName,
            'project_detail' => $detail,
        ];
    }

    $db->where('id', $projectId);
    $db->where('client_id', $clientId);
    $db->update('projects', [
        'project_name' => $projectName !== '' ? $projectName : (string) ($t['unnamed_project'] ?? 'Untitled Project'),
        'project_detail' => json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'last_update_at' => date('Y-m-d H:i:s'),
    ]);

    return [
        'ok' => true,
        'updated' => true,
        'project_name' => $projectName,
        'project_detail' => $detail,
    ];
}


function neejou_resolve_codes_from_labels(MysqliDb $db, string $table, array $labels): array
{
    $rows = $db->rawQuery('SELECT code, display_name FROM ' . $table);
    if (!is_array($rows)) return [];

    $map = [];
    foreach ($rows as $row) {
        if (!is_array($row)) continue;
        $code = trim((string) ($row['code'] ?? ''));
        $name = trim((string) ($row['display_name'] ?? ''));
        if ($code === '') continue;
        $map[neejou_normalize_label($code)] = $code;
        if ($name !== '') $map[neejou_normalize_label($name)] = $code;
    }

    $out = [];
    foreach ($labels as $label) {
        $k = neejou_normalize_label((string) $label);
        if ($k === '' || !isset($map[$k])) continue;
        $out[] = (string) $map[$k];
    }

    return array_values(array_unique($out));
}

function neejou_parse_github_repo_for_match(string $url): ?array
{
    $url = trim($url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    $parts = parse_url($url);
    if (!is_array($parts)) {
        return null;
    }

    $host = strtolower((string) ($parts['host'] ?? ''));
    if (!in_array($host, ['github.com', 'www.github.com'], true)) {
        return null;
    }

    $path = trim((string) ($parts['path'] ?? ''), '/');
    $segs = explode('/', $path);
    if (count($segs) < 2) {
        return null;
    }

    $owner = trim((string) ($segs[0] ?? ''));
    $repo = trim((string) ($segs[1] ?? ''));
    $repo = preg_replace('/\.git$/i', '', $repo) ?? $repo;
    if ($owner === '' || $repo === '') {
        return null;
    }

    return ['owner' => $owner, 'repo' => $repo];
}

function neejou_http_get_json_with_headers(string $url): array
{
    $headers = [];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Accept: application/vnd.github+json',
            'User-Agent: neejou-app',
        ],
        CURLOPT_HEADERFUNCTION => static function ($ch, string $line) use (&$headers): int {
            $len = strlen($line);
            $line = trim($line);
            if ($line === '' || strpos($line, ':') === false) {
                return $len;
            }
            [$k, $v] = explode(':', $line, 2);
            $headers[strtolower(trim($k))] = trim($v);
            return $len;
        },
    ]);

    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if (!is_string($raw) || $raw === '') {
        return ['status' => $status, 'data' => null, 'headers' => $headers, 'error' => $error !== '' ? $error : 'empty_response'];
    }

    $data = json_decode($raw, true);
    if (!is_array($data) && !is_object($data)) {
        return ['status' => $status, 'data' => null, 'headers' => $headers, 'error' => 'invalid_json'];
    }

    return ['status' => $status, 'data' => $data, 'headers' => $headers, 'error' => ''];
}

function neejou_github_commit_count(string $owner, string $repo): int
{
    $url = 'https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo) . '/commits?per_page=1';
    $res = neejou_http_get_json_with_headers($url);
    $status = (int) ($res['status'] ?? 0);
    if ($status >= 400) {
        return 0;
    }

    $link = strtolower((string) (($res['headers']['link'] ?? '')));
    if ($link !== '' && preg_match('/[?&]page=(\d+)>; rel="last"/i', $link, $m)) {
        return max(0, (int) ($m[1] ?? 0));
    }

    $data = $res['data'] ?? null;
    if (is_array($data)) {
        return count($data);
    }
    return 0;
}

function neejou_github_repo_snapshot(string $repoUrl): array
{
    $parsed = neejou_parse_github_repo_for_match($repoUrl);
    if (!$parsed) {
        return [
            'repo_url' => $repoUrl,
            'tree_files' => [],
            'readme' => '',
            'commit_count' => 0,
        ];
    }

    $owner = (string) $parsed['owner'];
    $repo = (string) $parsed['repo'];

    $repoMetaRes = neejou_http_get_json_with_headers('https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo));
    $defaultBranch = 'main';
    if ((int) ($repoMetaRes['status'] ?? 0) < 400 && is_array($repoMetaRes['data'] ?? null)) {
        $defaultBranch = trim((string) (($repoMetaRes['data']['default_branch'] ?? 'main')));
        if ($defaultBranch === '') {
            $defaultBranch = 'main';
        }
    }

    $treeFiles = [];
    $treeRes = neejou_http_get_json_with_headers(
        'https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo)
        . '/git/trees/' . rawurlencode($defaultBranch) . '?recursive=1'
    );
    if ((int) ($treeRes['status'] ?? 0) < 400 && is_array($treeRes['data'] ?? null) && is_array($treeRes['data']['tree'] ?? null)) {
        foreach ((array) $treeRes['data']['tree'] as $node) {
            if (!is_array($node)) {
                continue;
            }
            if ((string) ($node['type'] ?? '') !== 'blob') {
                continue;
            }
            $p = trim((string) ($node['path'] ?? ''));
            if ($p === '') {
                continue;
            }
            $treeFiles[] = $p;
            if (count($treeFiles) >= 200) {
                break;
            }
        }
    }

    $readme = '';
    $readmeRes = neejou_http_get_json_with_headers('https://api.github.com/repos/' . rawurlencode($owner) . '/' . rawurlencode($repo) . '/readme');
    if ((int) ($readmeRes['status'] ?? 0) < 400 && is_array($readmeRes['data'] ?? null)) {
        $enc = strtolower(trim((string) ($readmeRes['data']['encoding'] ?? '')));
        $content = (string) ($readmeRes['data']['content'] ?? '');
        if ($enc === 'base64' && $content !== '') {
            $decoded = base64_decode(str_replace(["\r", "\n"], '', $content), true);
            if (is_string($decoded)) {
                $readme = $decoded;
            }
        }
    }
    if ($readme !== '') {
        if (function_exists('mb_substr')) {
            $readme = (string) mb_substr($readme, 0, 4000, 'UTF-8');
        } else {
            $readme = substr($readme, 0, 4000);
        }
    }

    return [
        'repo_url' => 'https://github.com/' . $owner . '/' . $repo,
        'tree_files' => $treeFiles,
        'readme' => $readme,
        'commit_count' => neejou_github_commit_count($owner, $repo),
    ];
}

function neejou_match_engineers_query(
    MysqliDb $db,
    string $categoryCode,
    string $languageCode,
    ?string $databaseCode,
    int $budgetUsd,
    bool $acceptForeign,
    ?string $clientCountry,
    bool $existingProject
): array {
    $sql = 'SELECT DISTINCT '
        . 'rl.id AS repo_id, rl.repo_name, rl.repo_url, rl.engineer_id, '
        . 'pc.code AS matched_category, l.code AS matched_language'
        . ($databaseCode !== null ? ', dbt.code AS matched_database' : ', NULL AS matched_database')
        . ' FROM repo_list rl '
        . 'JOIN engineers e ON e.id = rl.engineer_id '
        . 'JOIN repo_project_categories rpc ON rpc.repo_id = rl.id '
        . 'JOIN project_categories pc ON pc.id = rpc.category_id '
        . 'JOIN repo_languages rlg ON rlg.repo_id = rl.id '
        . 'JOIN languages l ON l.id = rlg.language_id ';

    if ($databaseCode !== null) {
        $sql .= 'JOIN repo_databases rd ON rd.repo_id = rl.id '
            . 'JOIN `databases` dbt ON dbt.id = rd.database_id ';
    }

    $sql .= 'WHERE pc.code = ? AND l.code = ? ';
    $params = [$categoryCode, $languageCode];

    if ($databaseCode !== null) {
        $sql .= 'AND dbt.code = ? ';
        $params[] = $databaseCode;
    }

    $sql .= 'AND e.min_project_budget_usd <= ? ';
    $params[] = $budgetUsd;

    if ($existingProject) {
        $sql .= 'AND e.new_project_only = 0 ';
    }

    if (!$acceptForeign && $clientCountry !== null && $clientCountry !== '') {
        $sql .= 'AND e.country = ? ';
        $params[] = strtoupper($clientCountry);
    }

    $sql .= 'ORDER BY RAND() LIMIT 3';

    $rows = $db->rawQuery($sql, $params);
    return is_array($rows) ? $rows : [];
}

function neejou_evaluate_repos_with_ai(array $projectSummary, array $repos): array
{
    if (empty($repos)) {
        return ['ok' => false, 'message' => 'empty_repos'];
    }

    $apiKey = neejou_load_openai_key();
    if ($apiKey === '') {
        return ['ok' => false, 'message' => 'missing_openai_key'];
    }

    $evaluationPrompt = 'Evaluate repository compatibility with the client project summary and choose exactly one best repo. '
        . 'Score must be an integer from 0 to 100 and should not be 0 when category/language/database align well. '
        . 'Reason must include two parts: (1) a brief repository description, (2) why it fits this client project beyond only language/database. '
        . 'Use concrete evidence from repo data (category, language, database fit, file structure, README, commit_count), 2-5 sentences, max 300 characters.';

    $phasePayload = [
        'phase' => 'repo_evaluation',
        'client_project' => $projectSummary,
        'repos' => $repos,
        'evaluation_prompt' => $evaluationPrompt,
    ];

    $instructions = <<<'PROMPT'
------------------------------------------------------------
0. Two-Phase Operation (IMPORTANT)
------------------------------------------------------------

This assistant operates in two phases:

PHASE A) Project Setup (default)
- Follow all project setup rules in this system prompt.
- Your goal is to refine and stabilize the project summary JSON.

PHASE B) Repository Evaluation (only when explicitly triggered)
- This phase is triggered ONLY when the tool output (role: tool) includes:
  - "phase": "repo_evaluation"
  - and a non-empty "repos" list.

In PHASE B:
- Your responsibility expands to evaluating provided repositories for compatibility with the client project.
- You must select ONLY ONE best repository from the provided list.
- You must follow the evaluation prompt/instructions embedded in the tool output (e.g., "evaluation_prompt").
- You must NOT ask the user additional questions.
- You must return STRICT JSON only with:
  {
    "repo_id": number,
    "score": integer,
    "reason": "max 300 characters; include repo brief + why it fits the client project"
  }

If the tool output indicates "phase": "repo_evaluation" but repos is empty:
- Clearly state that no repository data is available and provide practical next steps.
- Do NOT fabricate repositories.
PROMPT;

    $payload = [
        'model' => 'gpt-5.2',
        'instructions' => $instructions,
        'input' => [
            [
                'role' => 'tool',
                'content' => json_encode($phasePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
        ],
    ];

    $res = neejou_responses_post($apiKey, $payload);
    if (empty($res)) {
        return ['ok' => false, 'message' => 'eval_api_failed'];
    }

    $txt = trim(neejou_extract_response_text($res));
    if ($txt === '') {
        return ['ok' => false, 'message' => 'eval_empty'];
    }

    $parsed = json_decode($txt, true);
    if (!is_array($parsed) && preg_match('/\{[\s\S]*\}/', $txt, $m)) {
        $parsed = json_decode((string) $m[0], true);
    }
    if (!is_array($parsed)) {
        return ['ok' => false, 'message' => 'eval_invalid_json', 'raw' => $txt];
    }

    $repoId = (int) ($parsed['repo_id'] ?? 0);
    $score = (int) ($parsed['score'] ?? 0);
    $reason = trim((string) ($parsed['reason'] ?? ''));
    if ($repoId <= 0) {
        return ['ok' => false, 'message' => 'eval_missing_repo_id', 'raw' => $parsed];
    }

    return [
        'ok' => true,
        'repo_id' => $repoId,
        'score' => $score,
        'reason' => $reason,
    ];
}

function neejou_text_length(string $text): int
{
    if (function_exists('mb_strlen')) {
        return (int) mb_strlen($text, 'UTF-8');
    }
    return strlen($text);
}

function neejou_text_substr(string $text, int $max): string
{
    if ($max <= 0) {
        return '';
    }
    if (function_exists('mb_substr')) {
        return (string) mb_substr($text, 0, $max, 'UTF-8');
    }
    return substr($text, 0, $max);
}

function neejou_repo_readme_brief(string $readme): string
{
    $raw = trim($readme);
    if ($raw === '') {
        return '';
    }

    $lines = preg_split('/\R/u', $raw);
    if (!is_array($lines)) {
        $lines = [$raw];
    }

    foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line === '') continue;
        $line = preg_replace('/^#+\s*/u', '', $line) ?? $line;
        $line = preg_replace('/[`*_>#\-\[\]]+/u', ' ', $line) ?? $line;
        $line = preg_replace('/\s+/u', ' ', trim($line)) ?? trim($line);
        if ($line !== '') {
            return neejou_text_substr($line, 120);
        }
    }

    return neejou_text_substr($raw, 120);
}

function neejou_repo_eval_fallback(array $candidate, bool $isZhTw): array
{
    $score = 40;
    $cat = trim((string) ($candidate['matched_category'] ?? ''));
    $lang = trim((string) ($candidate['matched_language'] ?? ''));
    $db = trim((string) ($candidate['matched_database'] ?? ''));

    if ($cat !== '') $score += 20;
    if ($lang !== '') $score += 20;
    if ($db !== '') {
        $score += 10;
    }

    $commitCount = (int) ($candidate['commit_count'] ?? 0);
    if ($commitCount >= 50) {
        $score += 8;
    } elseif ($commitCount >= 10) {
        $score += 5;
    } elseif ($commitCount > 0) {
        $score += 3;
    }

    $treeCount = 0;
    if (is_array($candidate['directory_tree'] ?? null)) {
        $treeCount = count((array) $candidate['directory_tree']);
    }
    if ($treeCount >= 50) {
        $score += 6;
    } elseif ($treeCount >= 10) {
        $score += 4;
    } elseif ($treeCount > 0) {
        $score += 2;
    }

    $readme = trim((string) ($candidate['readme'] ?? ''));
    if ($readme !== '') {
        $score += 6;
    }

    $score = max(20, min(90, $score));

    $repoName = trim((string) ($candidate['repo_name'] ?? ''));
    $brief = neejou_repo_readme_brief($readme);
    $briefText = $brief !== ''
        ? $brief
        : ($isZhTw
            ? '此 Repo 為完整應用專案，具備可交付程式結構。'
            : 'This repository appears to be a deliverable application project with practical implementation structure.');

    $reason = $isZhTw
        ? ('Repo 簡介：' . $repoName . '，' . $briefText
            . '。適配原因：與業主需求的類別、語言' . ($db !== '' ? '與資料庫' : '')
            . '一致，且可從檔案結構、README 與 commit 次數看到持續開發與可維護性，因此適合此專案。')
        : ('Repo brief: ' . $repoName . ', ' . $briefText
            . '. Why fit: it aligns with the required category and language' . ($db !== '' ? ', plus database' : '')
            . ', and shows practical delivery evidence from file structure, README, and commit activity, so it is suitable for this client project.');

    if (neejou_text_length($reason) > 300) {
        $reason = neejou_text_substr($reason, 300);
    }

    return ['score' => $score, 'reason' => $reason];
}
function neejou_load_engineer_card_by_repo(MysqliDb $db, int $repoId): ?array
{
    $rows = $db->rawQuery(
        'SELECT rl.id AS repo_id, rl.repo_name, rl.repo_url, rl.engineer_id, '
        . 'COALESCE(NULLIF(e.name, ""), e.git_id) AS engineer_name, e.git_id, e.email, e.country, '
        . 'e.web_url, e.linkedin_url, e.phone_country_code, e.phone, e.whatsapp, e.line_id, '
        . 'GROUP_CONCAT(DISTINCT pc.code ORDER BY pc.code SEPARATOR ",") AS tag_categories, '
        . 'GROUP_CONCAT(DISTINCT l.code ORDER BY l.code SEPARATOR ",") AS tag_languages, '
        . 'GROUP_CONCAT(DISTINCT d.code ORDER BY d.code SEPARATOR ",") AS tag_databases '
        . 'FROM repo_list rl '
        . 'JOIN engineers e ON e.id = rl.engineer_id '
        . 'LEFT JOIN repo_project_categories rpc ON rpc.repo_id = rl.id '
        . 'LEFT JOIN project_categories pc ON pc.id = rpc.category_id '
        . 'LEFT JOIN repo_languages rlg ON rlg.repo_id = rl.id '
        . 'LEFT JOIN languages l ON l.id = rlg.language_id '
        . 'LEFT JOIN repo_databases rd ON rd.repo_id = rl.id '
        . 'LEFT JOIN `databases` d ON d.id = rd.database_id '
        . 'WHERE rl.id = ? '
        . 'GROUP BY rl.id, rl.repo_name, rl.repo_url, rl.engineer_id, e.name, e.git_id, e.email, e.country, e.web_url, e.linkedin_url, e.phone_country_code, e.phone, e.whatsapp, e.line_id '
        . 'LIMIT 1',
        [$repoId]
    );

    if (!is_array($rows) || empty($rows) || !is_array($rows[0])) {
        return null;
    }
    return $rows[0];
}

function neejou_match_engineers_tool(MysqliDb $db, int $clientId, int $projectId, array $t, bool $isZhTw): array
{
    $row = neejou_load_project_row($db, $clientId, $projectId);
    if (!$row) {
        return ['ok' => false, 'message' => 'project_not_found'];
    }

    $detail = [];
    if (!empty($row['project_detail'])) {
        $decoded = json_decode((string) $row['project_detail'], true);
        if (is_array($decoded)) {
            $detail = $decoded;
        }
    }

    $summary = neejou_project_summary_from_detail($detail, (string) ($row['project_name'] ?? ''));

    $typeLabels = $detail['types'] ?? [];
    if (!is_array($typeLabels)) $typeLabels = [];
    $typeCodes = neejou_resolve_codes_from_labels($db, 'project_categories', $typeLabels);

    $languageLabels = $summary['languages'] ?? [];
    if (!is_array($languageLabels)) $languageLabels = [];
    $languageCodes = neejou_resolve_codes_from_labels($db, 'languages', $languageLabels);

    $databaseLabels = $summary['database'] ?? [];
    if (!is_array($databaseLabels)) $databaseLabels = [];
    $databaseCodes = neejou_resolve_codes_from_labels($db, '`databases`', $databaseLabels);

    $budgetRaw = trim((string) ($summary['budget'] ?? ''));
    $budgetUsd = 0;
    if ($budgetRaw !== '' && preg_match('/^\d+(?:\.\d+)?$/', $budgetRaw)) {
        $budgetUsd = (int) floor((float) $budgetRaw);
    }

    $cross = strtolower(trim((string) ($summary['cross_border_engineer'] ?? '')));
    $acceptForeign = $cross === 'yes';

    $db->where('id', $clientId);
    $clientRow = $db->getOne('clients', ['country']);
    $clientCountry = is_array($clientRow) ? strtoupper(trim((string) ($clientRow['country'] ?? ''))) : '';

    $mode = strtolower(trim((string) ($summary['project_type'] ?? '')));
    $existingProject = $mode === 'existing';

    if (empty($typeCodes) || empty($languageCodes)) {
        return [
            'ok' => true,
            'matched' => false,
            'reason' => 'insufficient_summary',
            'message' => $isZhTw ? '目前專案摘要不足，至少需要專案類別與語言。' : 'Project summary is insufficient. At least project category and language are required.',
            'requested' => [
                'types' => $typeCodes,
                'languages' => $languageCodes,
                'databases' => $databaseCodes,
            ],
        ];
    }

    $languageCode = (string) $languageCodes[0];
    $databaseCode = !empty($databaseCodes) ? (string) $databaseCodes[0] : null;

    $rows = [];
    $stage = '';

    if ($databaseCode !== null) {
        foreach ($typeCodes as $typeCode) {
            $rows = neejou_match_engineers_query($db, (string) $typeCode, $languageCode, $databaseCode, $budgetUsd, $acceptForeign, $clientCountry, $existingProject);
            if (!empty($rows)) {
                $stage = 'category_language_database';
                break;
            }
        }
    }

    if (empty($rows)) {
        foreach ($typeCodes as $typeCode) {
            $rows = neejou_match_engineers_query($db, (string) $typeCode, $languageCode, null, $budgetUsd, $acceptForeign, $clientCountry, $existingProject);
            if (!empty($rows)) {
                $stage = 'category_language';
                break;
            }
        }
    }

    if (empty($rows)) {
        return [
            'ok' => true,
            'matched' => false,
            'reason' => 'no_match',
            'message' => $isZhTw
                ? ('目前在你定義的類別內，找不到使用 ' . strtoupper($languageCode) . ' 的合適工程師資料。')
                : ('No suitable engineers were found in your selected categories with language ' . strtoupper($languageCode) . '.'),
            'requested' => [
                'types' => $typeCodes,
                'language' => $languageCode,
                'database' => $databaseCode,
                'budget_usd' => $budgetUsd,
                'accept_foreign' => $acceptForeign ? 'yes' : 'no',
                'client_country' => $clientCountry,
            ],
            'suggest_alternative_stack' => true,
        ];
    }

    $reposForEval = [];
    $allowedRepoIds = [];
    $repoEvalMap = [];
    foreach ($rows as $r) {
        if (!is_array($r)) continue;
        $repoId = (int) ($r['repo_id'] ?? 0);
        if ($repoId <= 0) continue;

        $snapshot = neejou_github_repo_snapshot((string) ($r['repo_url'] ?? ''));
        $candidate = [
            'repo_id' => $repoId,
            'repo_name' => (string) ($r['repo_name'] ?? ''),
            'repo_url' => (string) ($r['repo_url'] ?? ''),
            'directory_tree' => $snapshot['tree_files'] ?? [],
            'readme' => (string) ($snapshot['readme'] ?? ''),
            'commit_count' => (int) ($snapshot['commit_count'] ?? 0),
            'matched_category' => (string) ($r['matched_category'] ?? ''),
            'matched_language' => (string) ($r['matched_language'] ?? ''),
            'matched_database' => (string) ($r['matched_database'] ?? ''),
        ];
        $reposForEval[] = $candidate;
        $repoEvalMap[$repoId] = $candidate;
        $allowedRepoIds[$repoId] = true;
    }

    $eval = neejou_evaluate_repos_with_ai($summary, $reposForEval);
    $selectedRepoId = 0;
    $score = 0;
    $reason = '';

    if (!empty($eval['ok'])) {
        $candidateId = (int) ($eval['repo_id'] ?? 0);
        if ($candidateId > 0 && isset($allowedRepoIds[$candidateId])) {
            $selectedRepoId = $candidateId;
            $score = max(20, min(90, (int) ($eval['score'] ?? 0)));
            $reason = trim((string) ($eval['reason'] ?? ''));
        }
    }

    if ($selectedRepoId <= 0 && !empty($reposForEval[0]['repo_id'])) {
        $selectedRepoId = (int) $reposForEval[0]['repo_id'];
    }

    if ($selectedRepoId > 0) {
        $fallback = neejou_repo_eval_fallback($repoEvalMap[$selectedRepoId] ?? [], $isZhTw);
        $fallbackScore = (int) ($fallback['score'] ?? 70);
        $fallbackReason = trim((string) ($fallback['reason'] ?? ''));

        if ($score <= 0) {
            $score = $fallbackScore;
        }
        $score = max(20, min(90, $score));
        $reasonLower = strtolower($reason);
        $hasEvidence = strpos($reasonLower, 'commit') !== false
            || strpos($reasonLower, 'readme') !== false
            || strpos($reasonLower, 'file') !== false
            || strpos($reasonLower, 'structure') !== false
            || strpos($reasonLower, 'fit') !== false
            || strpos($reasonLower, 'suitable') !== false
            || strpos($reason, '語言') !== false
            || strpos($reason, '資料庫') !== false
            || strpos($reason, '類別') !== false
            || strpos($reason, '適合') !== false
            || strpos($reason, '專案') !== false;
        if ($reason === '' || neejou_text_length($reason) < 40 || !$hasEvidence) {
            $reason = $fallbackReason;
        }
    if (neejou_text_length($reason) > 300) {
        $reason = neejou_text_substr($reason, 300);
    }
    }

    if ($reason === '') {
        $reason = $isZhTw ? '已完成契合度分析。' : 'Compatibility analysis is completed.';
    }

    $card = $selectedRepoId > 0 ? neejou_load_engineer_card_by_repo($db, $selectedRepoId) : null;
    if (!is_array($card)) {
        return [
            'ok' => true,
            'matched' => false,
            'reason' => 'repo_selected_but_profile_missing',
            'message' => $isZhTw ? '已完成 Repo 評估，但目前無法取得工程師聯絡資料。' : 'Repository evaluation is done, but engineer profile is currently unavailable.',
            'stage' => $stage,
        ];
    }

    $card['score'] = $score;
    $card['reason_text'] = $reason;

    return [
        'ok' => true,
        'matched' => true,
        'stage' => $stage,
        'count' => 1,
        'items' => [$card],
        'requested' => [
            'types' => $typeCodes,
            'language' => $languageCode,
            'database' => $databaseCode,
            'budget_usd' => $budgetUsd,
            'accept_foreign' => $acceptForeign ? 'yes' : 'no',
            'client_country' => $clientCountry,
        ],
        'repo_eval' => [
            'phase' => 'repo_evaluation',
            'candidate_repo_count' => count($reposForEval),
            'selected_repo_id' => $selectedRepoId,
            'score' => $score,
            'reason' => $reason,
        ],
    ];
}

function neejou_is_explicit_match_intent(string $text): bool
{
    $t = strtolower(trim($text));
    if ($t === '') return false;

    $phrases = [
        '開始配對', '進行配對', '工程師配對', '立即配對', '馬上配對',
        'start matching', 'start match', 'match engineers', 'go matching', 'do matching'
    ];
    foreach ($phrases as $p) {
        if (strpos($t, strtolower($p)) !== false) return true;
    }

    if (strpos($t, '配對') !== false && (strpos($t, '開始') !== false || strpos($t, '進行') !== false || strpos($t, '可以') !== false || strpos($t, '好') !== false)) {
        return true;
    }

    return false;
}

function neejou_country_names(string $code): array
{
    $map = [
        'US' => ['美國', 'United States'],
        'TW' => ['台灣', 'Taiwan'],
        'JP' => ['日本', 'Japan'],
        'KR' => ['韓國', 'South Korea'],
        'SG' => ['新加坡', 'Singapore'],
        'HK' => ['香港', 'Hong Kong'],
        'CN' => ['中國', 'China'],
        'TH' => ['泰國', 'Thailand'],
        'VN' => ['越南', 'Vietnam'],
        'MY' => ['馬來西亞', 'Malaysia'],
        'PH' => ['菲律賓', 'Philippines'],
        'IN' => ['印度', 'India'],
        'AU' => ['澳洲', 'Australia'],
        'NZ' => ['紐西蘭', 'New Zealand'],
        'CA' => ['加拿大', 'Canada'],
        'GB' => ['英國', 'United Kingdom'],
        'DE' => ['德國', 'Germany'],
        'FR' => ['法國', 'France'],
        'NL' => ['荷蘭', 'Netherlands'],
        'BR' => ['巴西', 'Brazil'],
    ];
    $code = strtoupper(trim($code));
    if (isset($map[$code])) {
        return ['zh' => $map[$code][0], 'en' => $map[$code][1]];
    }
}

function neejou_format_match_result_text(array $result, bool $isZhTw): string
{
    $items = $result['items'] ?? [];
    if (!is_array($items) || empty($items)) {
        return $isZhTw
            ? '目前找不到符合條件的工程師資料。你可以調整語言、資料庫、預算或類別後再試一次。'
            : 'No matching engineers were found. You can adjust language, database, budget, or categories and try again.';
    }

    $metaItems = [];
    foreach ($items as $it) {
        if (!is_array($it)) {
            continue;
        }

        $gitId = trim((string) ($it['git_id'] ?? ''));
        $name = trim((string) ($it['engineer_name'] ?? $gitId));
        $repoName = trim((string) ($it['repo_name'] ?? ''));
        $repoUrl = trim((string) ($it['repo_url'] ?? ''));
        $email = trim((string) ($it['email'] ?? ''));
        if ($name === '' || $repoName === '' || $repoUrl === '' || $email === '') {
            continue;
        }

        $countryCode = strtoupper(trim((string) ($it['country'] ?? '')));
        $country = neejou_country_names($countryCode);
        $countryDisplay = trim((string) ($country['en'] ?? $countryCode));

        $phoneCode = trim((string) ($it['phone_country_code'] ?? ''));
        $phone = trim((string) ($it['phone'] ?? ''));
        $phoneDisplay = trim($phoneCode . ' ' . $phone);
        $phoneTel = '';
        if ($phoneDisplay !== '') {
            $phoneTel = preg_replace('/[^0-9+]/', '', $phoneDisplay) ?? '';
        }

        $tags = [];
        foreach (['tag_categories', 'tag_languages', 'tag_databases'] as $k) {
            $raw = trim((string) ($it[$k] ?? ''));
            if ($raw === '') {
                continue;
            }
            foreach (explode(',', $raw) as $piece) {
                $piece = trim((string) $piece);
                if ($piece !== '') {
                    $tags[] = $piece;
                }
            }
        }
        $tags = array_values(array_unique($tags));

        $metaItems[] = [
            'name' => $name,
            'git_id' => $gitId,
            'avatar_url' => $gitId !== '' ? ('https://github.com/' . rawurlencode($gitId) . '.png?size=96') : '',
            'repo_name' => $repoName,
            'repo_url' => $repoUrl,
            'email' => $email,
            'country' => $countryDisplay,
            'web_url' => trim((string) ($it['web_url'] ?? '')),
            'linkedin_url' => trim((string) ($it['linkedin_url'] ?? '')),
            'phone_display' => $phoneDisplay,
            'phone_tel' => $phoneTel,
            'whatsapp' => trim((string) ($it['whatsapp'] ?? '')),
            'line_id' => trim((string) ($it['line_id'] ?? '')),
            'tags' => $tags,
            'score' => (int) ($it['score'] ?? 0),
            'reason' => trim((string) ($it['reason_text'] ?? '')),
        ];
    }

    if (empty($metaItems)) {
        return $isZhTw
            ? '目前找不到符合條件的工程師資料。你可以調整語言、資料庫、預算或類別後再試一次。'
            : 'No matching engineers were found. You can adjust language, database, budget, or categories and try again.';
    }

    $meta = [
        'title' => $isZhTw ? '已找到可配對的工程師資料' : 'Matching engineer candidates found',
        'items' => $metaItems,
    ];

    $fallbackLines = [
        $isZhTw ? '已找到可配對的工程師資料：' : 'Matching engineer candidates found:',
    ];
    foreach ($metaItems as $idx => $m) {
        $fallbackLines[] = '';
        $fallbackLines[] = ($isZhTw ? '候選 ' : 'Candidate ') . ($idx + 1) . '：';
        $fallbackLines[] = '- ' . ($isZhTw ? '工程師' : 'Engineer') . ': ' . $m['name'];
        $fallbackLines[] = '- Repo: ' . $m['repo_name'] . ' (' . $m['repo_url'] . ')';
        $fallbackLines[] = '- Score: ' . (int) ($m['score'] ?? 0) . '%';
    }

    return '[[NJ_MATCH_META_BASE64]]'
        . base64_encode((string) json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        . '[[/NJ_MATCH_META_BASE64]]'
        . "\n"
        . implode("\n", $fallbackLines);
}

function neejou_run_ai_with_tool(MysqliDb $db, int $clientId, int $projectId, string $apiKey, string $systemPrompt, array $history, array $t, bool $isZhTw): array
{
    $tools = [
        [
            'type' => 'function',
            'name' => 'update_project_detail',
            'description' => 'Update project summary fields in project_detail when the user confirms or changes decisions. Use ai_description for requirement decisions.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'project_name' => ['type' => 'string'],
                    'project_mode' => ['type' => 'string', 'enum' => ['new', 'existing']],
                    'project_type' => ['oneOf' => [['type' => 'string'], ['type' => 'array', 'items' => ['type' => 'string']]]],
                    'project_types' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'types' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'project_category' => ['oneOf' => [['type' => 'string'], ['type' => 'array', 'items' => ['type' => 'string']]]],
                    'languages' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'database' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'budget' => ['type' => 'string'],
                    'cross_border_engineer' => ['type' => 'string', 'enum' => ['yes', 'no']],
                    'ai_description_append' => ['type' => 'string'],
                    'ai_description_replace' => ['type' => 'string'],
                ],
                'additionalProperties' => false,
            ],
        ],

        [
            'type' => 'function',
            'name' => 'match_engineers',
            'description' => 'Match engineers based on project summary and return up to 3 candidate repos/engineers.',
            'parameters' => [
                'type' => 'object',
                'properties' => [],
                'additionalProperties' => false,
            ],
        ],
    ];

    $input = array_map(static function ($m) {
        return [
            'role' => (string) ($m['role'] ?? 'user'),
            'content' => (string) ($m['content'] ?? ''),
        ];
    }, $history);

    $assistantText = '';
    $updated = false;
    $updatedDetail = null;
    $updatedName = null;
    $previousResponseId = null;
    $matchExecuted = false;
    $lastMatchResult = null;

    for ($i = 0; $i < 3; $i++) {
        $payload = [
            'model' => 'gpt-5.2',
            'instructions' => $systemPrompt,
            'input' => $input,
            'tools' => $tools,
        ];
        if ($previousResponseId) {
            $payload['previous_response_id'] = $previousResponseId;
            unset($payload['instructions']);
        }

        $res = neejou_responses_post($apiKey, $payload);
        if (empty($res)) {
            break;
        }

        $previousResponseId = (string) ($res['id'] ?? '');
        $text = neejou_extract_response_text($res);
        if ($text !== '') {
            $assistantText = $text;
        }

        $calls = neejou_extract_tool_calls($res);
        if (empty($calls)) {
            break;
        }

        $toolOutputs = [];
        foreach ($calls as $call) {
            $name = (string) ($call['name'] ?? '');
            $args = json_decode((string) ($call['arguments'] ?? '{}'), true);
            if (!is_array($args)) {
                $args = [];
            }

            if ($name === 'update_project_detail') {
                $result = neejou_apply_update_project_detail_tool($db, $clientId, $projectId, $args, $t);
                if (!empty($result['updated'])) {
                    $updated = true;
                }
                if (isset($result['project_detail']) && is_array($result['project_detail'])) {
                    $updatedDetail = $result['project_detail'];
                }
                if (isset($result['project_name'])) {
                    $updatedName = (string) $result['project_name'];
                }
            } elseif ($name === 'match_engineers') {
                $result = neejou_match_engineers_tool($db, $clientId, $projectId, $t, $isZhTw);
                $matchExecuted = true;
                $lastMatchResult = $result;
            } else {
                continue;
            }

            $toolOutputs[] = [
                'type' => 'function_call_output',
                'call_id' => (string) ($call['call_id'] ?? ''),
                'output' => json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
        }

        if (empty($toolOutputs)) {
            break;
        }

        $input = $toolOutputs;
    }

    if ($assistantText === '') {
        $plainPayload = [
            'model' => 'gpt-5.2',
            'instructions' => $systemPrompt,
            'input' => array_map(static function ($m) {
                return [
                    'role' => (string) ($m['role'] ?? 'user'),
                    'content' => (string) ($m['content'] ?? ''),
                ];
            }, $history),
        ];
        $plainRes = neejou_responses_post($apiKey, $plainPayload);
        $plainText = neejou_extract_response_text($plainRes);
        if ($plainText !== '') {
            $assistantText = $plainText;
        }
    }


    // Semantic sync pass: if no update tool was executed in main response,
    // run a strict sync step so AI can infer and persist summary fields from intent.
    if (!$updated) {
        $syncInstructions = "You are a strict project-summary sync engine for neeJou. "
            . "Based on the conversation and current project summary, decide whether summary fields should be updated. "
            . "If updates are needed, call update_project_detail with only changed fields. "
            . "For category inference, include multiple RELATED types when clearly relevant to the user's scenario, and never add unrelated types. "
            . "Do not output prose unless no update is needed.";

        $syncInput = array_map(static function ($m) {
            return [
                'role' => (string) ($m['role'] ?? 'user'),
                'content' => (string) ($m['content'] ?? ''),
            ];
        }, $history);

        if (trim($assistantText) !== '') {
            $syncInput[] = ['role' => 'assistant', 'content' => $assistantText];
        }

        $syncPayload = [
            'model' => 'gpt-5.2',
            'instructions' => $syncInstructions,
            'input' => $syncInput,
            'tools' => [$tools[0]],
        ];

        $syncRes = neejou_responses_post($apiKey, $syncPayload);
        $syncCalls = neejou_extract_tool_calls($syncRes);
        if (!empty($syncCalls)) {
            foreach ($syncCalls as $call) {
                if ((string) ($call['name'] ?? '') !== 'update_project_detail') {
                    continue;
                }

                $args = json_decode((string) ($call['arguments'] ?? '{}'), true);
                if (!is_array($args)) {
                    $args = [];
                }

                $result = neejou_apply_update_project_detail_tool($db, $clientId, $projectId, $args, $t);
                if (!empty($result['updated'])) {
                    $updated = true;
                }
                if (isset($result['project_detail']) && is_array($result['project_detail'])) {
                    $updatedDetail = $result['project_detail'];
                }
                if (isset($result['project_name'])) {
                    $updatedName = (string) $result['project_name'];
                }
            }
        }
    }

    $lastUserText = '';
    for ($j = count($history) - 1; $j >= 0; $j--) {
        $h = $history[$j] ?? null;
        if (is_array($h) && (($h['role'] ?? '') === 'user')) {
            $lastUserText = trim((string) ($h['content'] ?? ''));
            if ($lastUserText !== '') break;
        }
    }

    // Reliability guard: if user explicitly requests matching but model didn't actually call the tool,
    // force one real match execution and use real data result.
    if (!$matchExecuted && neejou_is_explicit_match_intent($lastUserText)) {
        $lastMatchResult = neejou_match_engineers_tool($db, $clientId, $projectId, $t, $isZhTw);
        $matchExecuted = true;
    }

    if ($matchExecuted && is_array($lastMatchResult)) {
        $assistantText = neejou_format_match_result_text($lastMatchResult, $isZhTw);
    }

    if ($assistantText === '') {
        $assistantText = $isZhTw ? '我收到你的補充，請再提供更具體的專案目標或限制。' : 'I received your input. Please provide more specific project goals or constraints.';
    }

    return [
        'assistant' => $assistantText,
        'summary_updated' => $updated,
        'project_detail' => $updatedDetail,
        'project_name' => $updatedName,
    ];
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

    if ($action === 'get_project') {
        header('Content-Type: application/json; charset=UTF-8');

        $projectId = (int) ($_POST['project_id'] ?? 0);
        if ($projectId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_project_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $project = $db->getOne('projects', ['id', 'project_name', 'project_detail', 'jai_history']);

        if (!is_array($project) || empty($project['id'])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'not_found'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $detail = [];
        if (!empty($project['project_detail'])) {
            $decodedDetail = json_decode((string) $project['project_detail'], true);
            if (is_array($decodedDetail)) {
                $detail = $decodedDetail;
            }
        }

        $history = [];
        if (!empty($project['jai_history'])) {
            $decodedHistory = json_decode((string) $project['jai_history'], true);
            if (is_array($decodedHistory)) {
                foreach ($decodedHistory as $item) {
                    if (!is_array($item)) continue;
                    $role = (string) ($item['role'] ?? '');
                    $content = (string) ($item['content'] ?? '');
                    if (!in_array($role, ['assistant', 'user'], true)) continue;
                    if ($content === '') continue;
                    $history[] = ['role' => $role, 'content' => $content];
                }
            }
        }

        echo json_encode([
            'ok' => true,
            'project' => [
                'id' => (int) $project['id'],
                'project_name' => (string) ($project['project_name'] ?? ''),
                'project_detail' => $detail,
                'jai_history' => $history,
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'list_projects') {
        header('Content-Type: application/json; charset=UTF-8');

        $db->where('client_id', $clientId);
        $db->orderBy('last_update_at', 'DESC');
        $rows = $db->get('projects', null, ['id', 'project_name', 'last_update_at']);
        if (!is_array($rows)) {
            $rows = [];
        }

        echo json_encode([
            'ok' => true,
            'projects' => $rows,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'ai_project_init_stream') {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        @ini_set('zlib.output_compression', '0');
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        ob_implicit_flush(true);

        $projectId = (int) ($_POST['project_id'] ?? 0);
        if ($projectId <= 0) {
            exit;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $project = $db->getOne('projects', ['id', 'project_name', 'project_detail', 'jai_history']);

        if (!is_array($project) || empty($project['id'])) {
            exit;
        }

        $detail = [];
        if (!empty($project['project_detail'])) {
            $decoded = json_decode((string) $project['project_detail'], true);
            if (is_array($decoded)) {
                $detail = $decoded;
            }
        }

        $history = [];
        if (!empty($project['jai_history'])) {
            $decodedHistory = json_decode((string) $project['jai_history'], true);
            if (is_array($decodedHistory)) {
                foreach ($decodedHistory as $item) {
                    if (!is_array($item)) continue;
                    $role = (string) ($item['role'] ?? '');
                    $content = trim((string) ($item['content'] ?? ''));
                    if (!in_array($role, ['assistant', 'user'], true) || $content === '') continue;
                    $history[] = ['role' => $role, 'content' => $content];
                }
            }
        }

        if (!empty($history)) {
            exit;
        }

        $summary = neejou_project_summary_from_detail($detail, (string) ($project['project_name'] ?? ''));
        $systemPrompt = neejou_build_system_prompt($summary, $isZhTw);
        $apiKey = neejou_load_openai_key();

        $assistantText = neejou_stream_responses_api(
            $apiKey,
            $systemPrompt,
            [
                ['role' => 'user', 'content' => $isZhTw ? '請根據專案摘要開始分析並給出下一步。' : 'Please start analyzing this project summary and provide practical next steps.'],
            ],
            static function (string $delta): void {
                echo $delta;
                flush();
            }
        );

        if ($assistantText === '') {
            $assistantText = $isZhTw ? '我已收到專案資料，接下來會先確認關鍵需求與技術可行性。' : 'I received the project summary. Next, I will confirm key requirements and technical feasibility.';
            echo $assistantText;
            flush();
        }

        $newHistory = [
            ['role' => 'assistant', 'content' => $assistantText],
        ];

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $db->update('projects', [
            'jai_history' => json_encode($newHistory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'last_update_at' => date('Y-m-d H:i:s'),
        ]);

        exit;
    }

    if ($action === 'ai_chat_user_stream') {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        @ini_set('zlib.output_compression', '0');
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        ob_implicit_flush(true);

        try {
            $projectId = (int) ($_POST['project_id'] ?? 0);
            $message = trim((string) ($_POST['message'] ?? ''));

        if ($projectId <= 0 || $message === '') {
            exit;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $project = $db->getOne('projects', ['id', 'project_name', 'project_detail', 'jai_history']);

        if (!is_array($project) || empty($project['id'])) {
            exit;
        }

        $detail = [];
        if (!empty($project['project_detail'])) {
            $decoded = json_decode((string) $project['project_detail'], true);
            if (is_array($decoded)) {
                $detail = $decoded;
            }
        }

        $history = [];
        if (!empty($project['jai_history'])) {
            $decodedHistory = json_decode((string) $project['jai_history'], true);
            if (is_array($decodedHistory)) {
                foreach ($decodedHistory as $item) {
                    if (!is_array($item)) continue;
                    $role = (string) ($item['role'] ?? '');
                    $content = trim((string) ($item['content'] ?? ''));
                    if (!in_array($role, ['assistant', 'user'], true) || $content === '') continue;
                    $history[] = ['role' => $role, 'content' => $content];
                }
            }
        }

        $history[] = ['role' => 'user', 'content' => $message];

        $summary = neejou_project_summary_from_detail($detail, (string) ($project['project_name'] ?? ''));
        $systemPrompt = neejou_build_system_prompt($summary, $isZhTw);
        $apiKey = neejou_load_openai_key();

        $aiResult = neejou_run_ai_with_tool($db, $clientId, $projectId, $apiKey, $systemPrompt, $history, $t, $isZhTw);
        $assistantText = (string) ($aiResult['assistant'] ?? '');

        $history[] = ['role' => 'assistant', 'content' => $assistantText];

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $db->update('projects', [
            'jai_history' => json_encode($history, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'last_update_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($aiResult['summary_updated'])) {
            echo "[[NJ_SUMMARY_UPDATED]]";
        }

        if (!empty($aiResult['project_name']) || !empty($aiResult['project_detail'])) {
            $meta = [
                'project_name' => (string) ($aiResult['project_name'] ?? ''),
                'project_detail' => is_array($aiResult['project_detail'] ?? null) ? $aiResult['project_detail'] : null,
            ];
            echo '[[NJ_SUMMARY_META_BASE64]]' . base64_encode(json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '[[/NJ_SUMMARY_META_BASE64]]';
        }

        $chars = preg_split('//u', $assistantText, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($chars)) {
            $chars = [];
        }

        $chunk = '';
        $count = 0;
        foreach ($chars as $ch) {
            $chunk .= $ch;
            $count++;
            if ($count >= 14) {
                echo $chunk;
                flush();
                $chunk = '';
                $count = 0;
                usleep(22000);
            }
        }
        if ($chunk !== '') {
            echo $chunk;
            flush();
        }

        exit;
        } catch (Throwable $e) {
            error_log('neejou ai_chat_user_stream error: ' . $e->getMessage());
            echo $isZhTw ? '系統暫時無法回覆，請稍後再試。' : 'The system cannot reply right now. Please try again shortly.';
            flush();
            exit;
        }
    }

    if ($action === 'save_history') {

        header('Content-Type: application/json; charset=UTF-8');

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $history = $_POST['history'] ?? [];

        if ($projectId <= 0 || !is_array($history)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_input'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $normalized = [];
        foreach ($history as $item) {
            if (!is_array($item)) continue;
            $role = (string) ($item['role'] ?? '');
            $content = trim((string) ($item['content'] ?? ''));
            if (!in_array($role, ['assistant', 'user'], true)) continue;
            if ($content === '') continue;
            $normalized[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $updated = $db->update('projects', [
            'jai_history' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'last_update_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode([
            'ok' => (bool) $updated,
            'project_id' => $projectId,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'rename_project') {
        header('Content-Type: application/json; charset=UTF-8');

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $projectName = trim((string) ($_POST['project_name'] ?? ''));

        if ($projectId <= 0 || $projectName === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_input'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $row = $db->getOne('projects', ['project_detail']);

        $projectDetailJson = null;
        if (is_array($row) && isset($row['project_detail'])) {
            $decoded = json_decode((string) $row['project_detail'], true);
            if (is_array($decoded)) {
                $decoded['project_name'] = $projectName;
                if (!array_key_exists('ai_description', $decoded)) {
                    $decoded['ai_description'] = '';
                }
                $projectDetailJson = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        $updateData = [
            'project_name' => $projectName,
            'last_update_at' => date('Y-m-d H:i:s'),
        ];
        if ($projectDetailJson !== null) {
            $updateData['project_detail'] = $projectDetailJson;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $updated = $db->update('projects', $updateData);

        echo json_encode([
            'ok' => (bool) $updated,
            'project_id' => $projectId,
            'project_name' => $projectName,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'delete_project') {
        header('Content-Type: application/json; charset=UTF-8');

        $projectId = (int) ($_POST['project_id'] ?? 0);
        if ($projectId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'invalid_project_id'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $db->where('id', $projectId);
        $db->where('client_id', $clientId);
        $deleted = $db->delete('projects', 1);

        echo json_encode([
            'ok' => (bool) $deleted,
            'project_id' => $projectId,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($action === 'save_project') {
        header('Content-Type: application/json; charset=UTF-8');

        $projectId = (int) ($_POST['project_id'] ?? 0);
        $projectName = trim((string) ($_POST['project_name'] ?? ''));

        $mode = trim((string) ($_POST['mode'] ?? ''));
        if (!in_array($mode, ['new', 'existing'], true)) {
            $mode = '';
        }

        $types = $_POST['types'] ?? [];
        if (!is_array($types)) {
            $types = [];
        }
        $types = array_values(array_filter(array_map(static function ($v) {
            return trim((string) $v);
        }, $types), static function ($v) {
            return $v !== '';
        }));
        $allowedTypeIds = neejou_supported_project_type_ids();
        $types = array_values(array_filter($types, static function ($v) use ($allowedTypeIds) {
            return in_array($v, $allowedTypeIds, true);
        }));


        $stacks = $_POST['stacks'] ?? [];
        if (!is_array($stacks)) {
            $stacks = [];
        }
        $stacks = array_values(array_filter(array_map(static function ($v) {
            return trim((string) $v);
        }, $stacks), static function ($v) {
            return $v !== '';
        }));

        $budget = trim((string) ($_POST['budget'] ?? ''));
        if ($budget !== '' && !preg_match('/^\d+(\.\d+)?$/', $budget)) {
            $budget = '';
        }
        $acceptForeign = trim((string) ($_POST['accept_foreign'] ?? ''));
        if (!in_array($acceptForeign, ['yes', 'no'], true)) {
            $acceptForeign = '';
        }

        if ($projectName === '') {
            $projectName = (string) $t['unnamed_project'];
        }

        $projectDetailArray = [
            'mode' => $mode,
            'project_name' => $projectName,
            'types' => $types,
            'stacks' => $stacks,
            'budget' => $budget,
            'accept_foreign' => $acceptForeign,
            'ai_description' => '',
            'completed' => (bool) ($_POST['completed'] ?? false),
        ];
        $projectDetailJson = json_encode($projectDetailArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $now = date('Y-m-d H:i:s');
        $savedProjectId = 0;

        if ($projectId > 0) {
            $db->where('id', $projectId);
            $db->where('client_id', $clientId);
            $updated = $db->update('projects', [
                'project_name' => $projectName,
                'project_detail' => $projectDetailJson,
                'last_update_at' => $now,
            ]);

            if ($updated) {
                $savedProjectId = $projectId;
            }
        }

        if ($savedProjectId === 0) {
            $savedProjectId = (int) $db->insert('projects', [
                'client_id' => $clientId,
                'project_name' => $projectName,
                'project_detail' => $projectDetailJson,
                'jai_history' => null,
                'engineers_list' => null,
                'created_at' => $now,
                'last_update_at' => $now,
            ]);
        }

        if ($savedProjectId <= 0) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'save_failed'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode([
            'ok' => true,
            'project_id' => $savedProjectId,
            'project_name' => $projectName,
            'project_detail' => $projectDetailArray,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

$userName = trim((string) ($_SESSION['client']['name'] ?? $_SESSION['client']['email'] ?? 'User'));
$userAvatar = trim((string) ($_SESSION['client']['picture'] ?? ''));
$userInitial = strtoupper(substr($userName !== '' ? $userName : 'U', 0, 1));

$view = (string) ($_GET['view'] ?? 'dashboard');
if (!in_array($view, ['dashboard', 'create-project'], true)) {
    $view = 'dashboard';
}
$selectedProjectId = (int) ($_GET['project_id'] ?? 0);
?>
<!doctype html>
<html lang="<?= $t['lang'] ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>neeJou - Client Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .md-content h1,.md-content h2,.md-content h3{font-weight:700;margin:1rem 0 .5rem}
    .md-content p{margin:.5rem 0;line-height:1.7}
    .md-content ul,.md-content ol{margin:.5rem 0 .5rem 1.2rem}
    .md-content li{margin:.25rem 0}
    .md-content code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,'Liberation Mono','Courier New',monospace;background:rgba(148,163,184,.2);padding:.1rem .35rem;border-radius:.35rem}
    .md-content pre{background:rgba(15,23,42,.75);padding:.9rem 1rem;border-radius:.7rem;overflow:auto;margin:.8rem 0}
    .md-content pre code{background:transparent;padding:0}
    .md-content a{text-decoration:underline}

    .hide-scrollbar{ -ms-overflow-style:none; scrollbar-width:none; }
    .hide-scrollbar::-webkit-scrollbar{ width:0 !important; height:0 !important; background:transparent; }
    .hide-scrollbar::-webkit-scrollbar-track{ background:transparent; }
    .hide-scrollbar::-webkit-scrollbar-thumb{ background:transparent; border:none; }
    .typing-cursor{ display:inline-block; margin-left:.12rem; color:#fff; font-size:1.06em; line-height:1; animation:blink-cursor 1s steps(1,end) infinite; }
    @keyframes blink-cursor{ 0%,49%{opacity:1;} 50%,100%{opacity:0;} }
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
      <a href="./client_dashboard.php?view=dashboard" class="menu-item inline-flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-sm transition <?= $view === 'dashboard' ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200' ?>">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z"/></svg>
        <span class="menu-label truncate"><?= htmlspecialchars($t['dashboard'], ENT_QUOTES, 'UTF-8') ?></span>
      </a>

      <a id="create-project-link" href="./client_dashboard.php?view=create-project" class="menu-item inline-flex w-full items-center gap-3 rounded-lg px-2.5 py-2 text-sm transition <?= $view === 'create-project' ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-500 hover:text-zinc-800 dark:text-zinc-500 dark:hover:text-zinc-200' ?>">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        <span class="menu-label truncate"><?= htmlspecialchars($t['create_project'], ENT_QUOTES, 'UTF-8') ?></span>
      </a>
    </nav>

    <div class="my-3 border-t border-zinc-200 dark:border-zinc-800"></div>
    <p class="menu-label px-2.5 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($t['project_list'], ENT_QUOTES, 'UTF-8') ?></p>
    <div id="project-list" class="mt-2 space-y-1"></div>
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
          <p id="navbar-project-title" class="mx-auto max-w-full truncate text-base font-medium text-zinc-600 dark:text-zinc-300"><?= $view === 'create-project' ? htmlspecialchars($t['unnamed_project'], ENT_QUOTES, 'UTF-8') : '' ?></p>
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
            <a href="./client_profile.php" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['profile'], ENT_QUOTES, 'UTF-8') ?></a>
            <a href="./logout.php" class="block rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['logout'], ENT_QUOTES, 'UTF-8') ?></a>
          </div>
        </div>
      </div>
    </header>

    <section class="p-4 sm:p-6">
      <?php if ($view === 'create-project'): ?>
        <div class="mx-auto flex h-[calc(100vh-140px)] w-full max-w-[820px] flex-col">
          <div id="chat-messages" class="hide-scrollbar flex-1 space-y-6 overflow-y-auto"></div>

          <form id="chat-form" class="mt-4 rounded-2xl bg-white/90 p-3 dark:bg-zinc-900/90">
            <div class="flex items-end gap-2">
              <textarea id="chat-input" rows="2" placeholder="<?= htmlspecialchars($t['ask_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="min-h-[52px] flex-1 resize-none rounded-xl bg-transparent px-3 py-2 text-sm outline-none placeholder:text-zinc-500 dark:placeholder:text-zinc-400 focus:ring-0"></textarea>
            </div>
          </form>
        </div>
      <?php else: ?>
        <?= $aboutHtml ?>
      <?php endif; ?>
    </section>
  </main>

  <div id="project-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-4xl rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xl dark:border-zinc-700 dark:bg-zinc-900 sm:p-7">
      <div class="flex items-center justify-between">
        <p class="text-sm text-zinc-500 dark:text-zinc-400"><span id="step-text"><?= htmlspecialchars($t['step'], ENT_QUOTES, 'UTF-8') ?> 1 / 3</span></p>
        <button id="close-project-modal" type="button" class="rounded-lg px-2 py-1 text-sm text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['close'], ENT_QUOTES, 'UTF-8') ?></button>
      </div>

      <p id="wizard-error" class="mt-3 hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/40 dark:text-red-300"></p>

      <form id="project-form" class="mt-5 space-y-6">
        <section data-step="1" class="wizard-step">
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($t['q1'], ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-4 space-y-3">
            <label class="flex items-center gap-3"><input type="radio" name="project_mode" value="new" class="h-4 w-4" /> <span><?= htmlspecialchars($t['new_dev'], ENT_QUOTES, 'UTF-8') ?></span></label>
            <label class="flex items-center gap-3"><input type="radio" name="project_mode" value="existing" class="h-4 w-4" /> <span><?= htmlspecialchars($t['existing'], ENT_QUOTES, 'UTF-8') ?></span></label>
          </div>
        </section>

        <section data-step="2" class="wizard-step hidden">
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($t['q2_type'], ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
            <?php foreach ($t['project_type_options'] as $typeOpt): ?>
              <label class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700"><input type="checkbox" name="project_types[]" value="<?= htmlspecialchars((string) $typeOpt['value'], ENT_QUOTES, 'UTF-8') ?>" class="h-4 w-4" /> <span><?= htmlspecialchars((string) $typeOpt['label'], ENT_QUOTES, 'UTF-8') ?></span></label>
            <?php endforeach; ?>
          </div>

          <h3 class="mt-6 text-lg font-semibold"><?= htmlspecialchars($t['q2_stack'], ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
            <?php foreach ($t['stacks'] as $stack): ?>
              <label class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700"><input type="checkbox" name="stacks[]" value="<?= htmlspecialchars($stack, ENT_QUOTES, 'UTF-8') ?>" class="h-4 w-4" /> <span><?= htmlspecialchars($stack, ENT_QUOTES, 'UTF-8') ?></span></label>
            <?php endforeach; ?>
          </div>
        </section>

        <section data-step="3" class="wizard-step hidden">
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($t['q3_project_name'], ENT_QUOTES, 'UTF-8') ?></h3>
          <input id="project-name-input" type="text" name="project_name" placeholder="<?= htmlspecialchars($t['project_name_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="mt-3 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" />

          <h3 class="mt-6 text-lg font-semibold"><?= htmlspecialchars($t['q3_budget'], ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-3 flex items-center gap-2">
            <input id="budget-input" type="text" name="budget" placeholder="<?= htmlspecialchars($t['budget_placeholder'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" />
            <span class="inline-flex h-[46px] items-center rounded-xl border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900">USD</span>
          </div>

          <h3 class="mt-6 text-lg font-semibold"><?= htmlspecialchars($t['q3_foreign'], ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="mt-3 flex items-center gap-6">
            <label class="inline-flex items-center gap-3"><input type="radio" name="accept_foreign" value="yes" class="h-4 w-4" /> <span><?= htmlspecialchars($t['yes'], ENT_QUOTES, 'UTF-8') ?></span></label>
            <label class="inline-flex items-center gap-3"><input type="radio" name="accept_foreign" value="no" class="h-4 w-4" /> <span><?= htmlspecialchars($t['no'], ENT_QUOTES, 'UTF-8') ?></span></label>
          </div>

          <p class="mt-5 text-sm text-zinc-500 dark:text-zinc-400"><?= htmlspecialchars($t['step3_note'], ENT_QUOTES, 'UTF-8') ?></p>
        </section>

        <div class="flex items-center justify-between pt-2">
          <button id="prev-step" type="button" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800" disabled><?= htmlspecialchars($t['prev'], ENT_QUOTES, 'UTF-8') ?></button>
          <button id="next-step" type="button" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['next'], ENT_QUOTES, 'UTF-8') ?></button>
          <button id="submit-project" type="button" class="hidden rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800"><?= htmlspecialchars($t['submit'], ENT_QUOTES, 'UTF-8') ?></button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
  <script>
    (function () {
      var panel = document.getElementById('left-panel');
      var content = document.getElementById('content');
      var toggle = document.getElementById('panel-toggle');
      var mobileToggle = document.getElementById('mobile-panel-toggle');
      var backdrop = document.getElementById('panel-backdrop');

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

      var userBtn = document.getElementById('user-menu-btn');
      var userMenu = document.getElementById('user-menu');
      var projectList = document.getElementById('project-list');
      var createProjectLink = document.getElementById('create-project-link');
      if (userBtn && userMenu) {
        userBtn.addEventListener('click', function (e) { e.stopPropagation(); userMenu.classList.toggle('hidden'); });
        document.addEventListener('click', function () { userMenu.classList.add('hidden'); closeProjectMenus(); });
      }

      createProjectLink && createProjectLink.addEventListener('click', function () {
        setTimeout(loadProjectList, 80);
      });

      var modal = document.getElementById('project-modal');
      var closeBtn = document.getElementById('close-project-modal');
      var stepText = document.getElementById('step-text');
      var prevBtn = document.getElementById('prev-step');
      var nextBtn = document.getElementById('next-step');
      var submitBtn = document.getElementById('submit-project');
      var errorBox = document.getElementById('wizard-error');
      var navbarProjectTitle = document.getElementById('navbar-project-title');
      var projectNameInput = document.getElementById('project-name-input');
      var unnamedProject = '<?= htmlspecialchars($t['unnamed_project'], ENT_QUOTES, 'UTF-8') ?>';
      var steps = Array.from(document.querySelectorAll('.wizard-step'));
      var currentStep = 1;
      var currentProjectId = null;
      var selectedProjectId = <?= (int) $selectedProjectId ?>;
      var isCreateProjectView = <?= $view === 'create-project' ? 'true' : 'false' ?>;

      var summaryLabels = {
        title: '<?= htmlspecialchars($t['summary_title'], ENT_QUOTES, 'UTF-8') ?>',
        projectName: '<?= htmlspecialchars($t['summary_project_name'], ENT_QUOTES, 'UTF-8') ?>',
        languages: '<?= htmlspecialchars($t['summary_languages'], ENT_QUOTES, 'UTF-8') ?>',
        database: '<?= htmlspecialchars($t['summary_database'], ENT_QUOTES, 'UTF-8') ?>',
        mode: '<?= htmlspecialchars($t['summary_mode'], ENT_QUOTES, 'UTF-8') ?>',
        types: '<?= htmlspecialchars($t['summary_types'], ENT_QUOTES, 'UTF-8') ?>',
        budget: '<?= htmlspecialchars($t['summary_budget'], ENT_QUOTES, 'UTF-8') ?>',
        foreign: '<?= htmlspecialchars($t['summary_foreign'], ENT_QUOTES, 'UTF-8') ?>',
        aiDescription: '<?= htmlspecialchars($t['summary_ai_description'], ENT_QUOTES, 'UTF-8') ?>'
      };

      var matchLabels = {
        repo: '<?= htmlspecialchars($isZhTw ? 'Repo 名稱 / 網址' : 'Repo Name / URL', ENT_QUOTES, 'UTF-8') ?>',
        score: '<?= htmlspecialchars($isZhTw ? '契合度' : 'Match Score', ENT_QUOTES, 'UTF-8') ?>',
        country: '<?= htmlspecialchars($isZhTw ? '國家' : 'Country', ENT_QUOTES, 'UTF-8') ?>',
        phone: '<?= htmlspecialchars($isZhTw ? '電話' : 'Phone', ENT_QUOTES, 'UTF-8') ?>',
        web: 'Web URL',
        linkedin: 'LinkedIn',
        email: 'Email',
        whatsapp: 'WhatsApp',
        line: 'Line ID'
      };

      function projectItemName(v) {
        var name = (v || '').trim();
        return name !== '' ? name : unnamedProject;
      }

      function closeProjectMenus() {
        document.querySelectorAll('[data-project-menu]').forEach(function (el) { el.classList.add('hidden'); });
      }

      function renderProjectList(items) {
        if (!projectList) return;
        projectList.innerHTML = '';

        if (!items || !items.length) {
          var empty = document.createElement('p');
          empty.className = 'px-2.5 py-1.5 text-xs text-zinc-500 dark:text-zinc-400';
          empty.textContent = '<?= htmlspecialchars($t['no_projects'], ENT_QUOTES, 'UTF-8') ?>';
          projectList.appendChild(empty);
          return;
        }

        function bindNameButton(btn, item) {
          btn.addEventListener('click', function () {
            var id = Number(item.id || 0) || 0;
            if (!id) return;
            window.location.href = './client_dashboard.php?view=create-project&project_id=' + id;
          });
        }

        function startRename(item, container) {
          var originalRaw = (item.project_name || '').trim();
          var originalDisplay = projectItemName(originalRaw);
          var input = document.createElement('input');
          input.type = 'text';
          input.value = originalDisplay;
          input.className = 'w-full rounded bg-transparent px-0 text-sm text-zinc-800 outline-none ring-0 placeholder:text-zinc-400 dark:text-zinc-100';

          container.innerHTML = '';
          container.appendChild(input);
          input.focus();
          input.select();

          var done = false;
          function restore(name) {
            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'min-w-0 w-full truncate text-left';
            nextBtn.textContent = projectItemName(name);
            container.innerHTML = '';
            container.appendChild(nextBtn);
            bindNameButton(nextBtn, item);
          }

          function commit() {
            if (done) return;
            done = true;
            var nextName = (input.value || '').trim();

            if (nextName === '') {
              restore(item.project_name || '');
              return;
            }

            if (nextName === (item.project_name || '').trim()) {
              restore(item.project_name || '');
              return;
            }

            fetch('./client_dashboard.php?view=create-project', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'rename_project', project_id: item.id, project_name: nextName })
            })
              .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('rename_failed')); })
              .then(function (res) {
                if (!res || !res.ok) {
                  restore(item.project_name || '');
                  return;
                }
                item.project_name = (res.project_name || '').trim();
                if (currentProjectId && Number(currentProjectId) === Number(item.id)) {
                  updateNavbarProjectTitle(item.project_name || '');
                }
                restore(item.project_name || '');
                loadProjectList();
              })
              .catch(function () {
                restore(item.project_name || '');
              });
          }

          function cancel() {
            if (done) return;
            done = true;
            restore(item.project_name || '');
          }

          input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              commit();
            }
            if (e.key === 'Escape') {
              e.preventDefault();
              cancel();
            }
          });

          input.addEventListener('blur', function () {
            commit();
          });
        }

        items.forEach(function (item) {
          var row = document.createElement('div');
          row.className = 'flex items-center justify-between rounded-lg px-2.5 py-2 text-sm text-zinc-600 hover:bg-zinc-200/60 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800/70 dark:hover:text-zinc-100';

          var nameWrap = document.createElement('div');
          nameWrap.className = 'min-w-0 flex-1';
          var nameBtn = document.createElement('button');
          nameBtn.type = 'button';
          nameBtn.className = 'min-w-0 w-full truncate text-left';
          nameBtn.textContent = projectItemName(item.project_name || '');
          bindNameButton(nameBtn, item);
          nameWrap.appendChild(nameBtn);
          row.appendChild(nameWrap);

          var menuWrap = document.createElement('div');
          menuWrap.className = 'relative ml-2';

          var menuBtn = document.createElement('button');
          menuBtn.type = 'button';
          menuBtn.className = 'rounded px-2 py-1 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-100';
          menuBtn.textContent = '⋮';
          menuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var menu = menuWrap.querySelector('[data-project-menu]');
            var isHidden = menu.classList.contains('hidden');
            closeProjectMenus();
            if (isHidden) menu.classList.remove('hidden');
          });
          menuWrap.appendChild(menuBtn);

          var menu = document.createElement('div');
          menu.setAttribute('data-project-menu', '1');
          menu.className = 'absolute right-0 top-7 z-20 hidden min-w-[140px] rounded-lg border border-zinc-200 bg-white p-1 shadow-lg dark:border-zinc-700 dark:bg-zinc-900';

          var renameBtn = document.createElement('button');
          renameBtn.type = 'button';
          renameBtn.className = 'block w-full rounded px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800';
          renameBtn.textContent = '<?= htmlspecialchars($t['rename'], ENT_QUOTES, 'UTF-8') ?>';
          renameBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeProjectMenus();
            startRename(item, nameWrap);
          });
          menu.appendChild(renameBtn);

          var delBtn = document.createElement('button');
          delBtn.type = 'button';
          delBtn.className = 'block w-full rounded px-3 py-2 text-left text-sm text-red-600 hover:bg-zinc-100 dark:text-red-400 dark:hover:bg-zinc-800';
          delBtn.textContent = '<?= htmlspecialchars($t['delete'], ENT_QUOTES, 'UTF-8') ?>';
          delBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeProjectMenus();
            if (!confirm('<?= htmlspecialchars($t['confirm_delete_project'], ENT_QUOTES, 'UTF-8') ?>')) return;

            var deletingCurrent = !!currentProjectId && Number(currentProjectId) === Number(item.id);
            var nextProjectId = 0;
            if (deletingCurrent) {
              var currIdx = items.findIndex(function (x) { return Number(x.id || 0) === Number(item.id); });
              if (currIdx !== -1 && items[currIdx + 1] && Number(items[currIdx + 1].id || 0) > 0) {
                nextProjectId = Number(items[currIdx + 1].id || 0);
              }
            }

            fetch('./client_dashboard.php?view=create-project', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'delete_project', project_id: item.id })
            })
              .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('delete_failed')); })
              .then(function (res) {
                if (!res || !res.ok) return;

                if (deletingCurrent) {
                  currentProjectId = null;
                  updateNavbarProjectTitle('');
                  messages = [];
                  renderMessages();
                }

                return loadProjectList().then(function () {
                  if (!deletingCurrent) return;
                  if (nextProjectId > 0) {
                    window.location.href = './client_dashboard.php?view=create-project&project_id=' + nextProjectId;
                    return;
                  }
                  window.location.href = './client_dashboard.php?view=dashboard';
                });
              })
              .catch(function () {});
          });
          menu.appendChild(delBtn);
          menuWrap.appendChild(menu);

          row.appendChild(menuWrap);
          projectList.appendChild(row);
        });
      }

      function loadProjectList() {
        if (!projectList) return Promise.resolve();
        return fetch('./client_dashboard.php?view=create-project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'list_projects' })
        })
          .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('list_failed')); })
          .then(function (res) {
            if (!res || !res.ok) throw new Error('list_failed');
            renderProjectList(Array.isArray(res.projects) ? res.projects : []);
          })
          .catch(function () {
            renderProjectList([]);
          });
      }

      function buildProjectSummary(detail, fallbackName) {
        var d = (detail && typeof detail === 'object') ? detail : {};
        var stacks = Array.isArray(d.stacks) ? d.stacks : [];
        var types = Array.isArray(d.types) ? d.types : [];
        var dbCandidates = {
          mysql: true,
          mariadb: true,
          postgresql: true,
          postgres: true,
          mongodb: true,
          redis: true,
          sqlite: true,
          oracle: true,
          sqlserver: true,
          'sql server': true,
          cockroachdb: true,
          cassandra: true,
          dynamodb: true,
          firebase: true,
          elasticsearch: true,
          opensearch: true,
          neo4j: true,
          db2: true,
          informix: true
        };

        var databases = [];
        var languages = [];
        stacks.forEach(function (item) {
          var label = String(item || '').trim();
          if (!label) return;
          var key = label.toLowerCase();
          if (dbCandidates[key]) {
            databases.push(label);
          } else {
            languages.push(label);
          }
        });

        var lines = [
          '## ' + summaryLabels.title,
          '',
          '- **' + summaryLabels.projectName + '：** ' + projectItemName(d.project_name || fallbackName || ''),
          '- **' + summaryLabels.languages + '：** ' + (languages.length ? languages.join(', ') : '-'),
          '- **' + summaryLabels.database + '：** ' + (databases.length ? databases.join(', ') : '-'),
          '- **' + summaryLabels.mode + '：** ' + (d.mode || '-'),
          '- **' + summaryLabels.types + '：** ' + (types.length ? types.join(', ') : '-'),
          '- **' + summaryLabels.budget + '：** ' + ((d.budget || '-') + ((d.budget || '').toString().trim() !== '' ? ' USD' : '')),
          '- **' + summaryLabels.foreign + '：** ' + (d.accept_foreign || '-'),
          '- **' + summaryLabels.aiDescription + '：** ' + (d.ai_description || '-')
        ];
        return lines.join('\n');
      }

      function upsertSummaryMessage(detail, projectName, moveToBottom) {
        var summaryText = buildProjectSummary(detail || {}, projectName || '');
        var idx = messages.findIndex(function (m) { return !!m.systemIntro; });

        if (idx === -1) {
          messages.push({ role: 'assistant', raw: summaryText, systemIntro: true, streaming: false });
          return;
        }

        messages[idx].raw = summaryText;
        if (!!moveToBottom && idx !== messages.length - 1) {
          var summaryMsg = messages.splice(idx, 1)[0];
          messages.push(summaryMsg);
        } else if (!moveToBottom && idx !== 0) {
          var topSummary = messages.splice(idx, 1)[0];
          messages.unshift(topSummary);
        }
      }

      function fetchLatestSummary(projectId) {
        if (!projectId) return Promise.resolve(null);
        return fetch('./client_dashboard.php?view=create-project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'get_project', project_id: projectId })
        })
          .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('summary_fetch_failed')); })
          .then(function (res) {
            if (!res || !res.ok || !res.project) return null;
            return {
              project_name: res.project.project_name || '',
              project_detail: (res.project.project_detail && typeof res.project.project_detail === 'object') ? res.project.project_detail : null
            };
          })
          .catch(function () { return null; });
      }

      function streamAssistant(payload) {
        setChatBusy(true);

        var assistantMsg = { role: 'assistant', raw: '', streaming: true };
        var summaryUpdated = false;
        var summaryMeta = null;
        var matchMeta = null;
        var ctrlBuffer = '';

        messages.push(assistantMsg);
        renderMessages();

        function b64ToUtf8(b64) {
          try {
            var bin = atob(b64);
            var bytes = Uint8Array.from(bin, function (c) { return c.charCodeAt(0); });
            return new TextDecoder('utf-8').decode(bytes);
          } catch (e) {
            return '';
          }
        }

        function consumeChunk(chunk) {
          ctrlBuffer += (chunk || '');

          if (ctrlBuffer.indexOf('[[NJ_SUMMARY_UPDATED]]') !== -1) {
            summaryUpdated = true;
            ctrlBuffer = ctrlBuffer.split('[[NJ_SUMMARY_UPDATED]]').join('');
          }

          var metaPrefix = '[[NJ_SUMMARY_META_BASE64]]';
          var metaSuffix = '[[/NJ_SUMMARY_META_BASE64]]';
          var matchPrefix = '[[NJ_MATCH_META_BASE64]]';
          var matchSuffix = '[[/NJ_MATCH_META_BASE64]]';

          while (true) {
            var start = ctrlBuffer.indexOf(metaPrefix);
            if (start === -1) break;
            var end = ctrlBuffer.indexOf(metaSuffix, start);
            if (end === -1) break;
            var b64 = ctrlBuffer.slice(start + metaPrefix.length, end);
            var jsonText = b64ToUtf8(b64);
            if (jsonText) {
              try { summaryMeta = JSON.parse(jsonText); } catch (e) {}
            }
            ctrlBuffer = ctrlBuffer.slice(0, start) + ctrlBuffer.slice(end + metaSuffix.length);
          }

          while (true) {
            var mStart = ctrlBuffer.indexOf(matchPrefix);
            if (mStart === -1) break;
            var mEnd = ctrlBuffer.indexOf(matchSuffix, mStart);
            if (mEnd === -1) break;
            var mb64 = ctrlBuffer.slice(mStart + matchPrefix.length, mEnd);
            var mjson = b64ToUtf8(mb64);
            if (mjson) {
              try { matchMeta = JSON.parse(mjson); } catch (e) {}
            }
            ctrlBuffer = ctrlBuffer.slice(0, mStart) + ctrlBuffer.slice(mEnd + matchSuffix.length);
          }

          var unsafeMarkerPos = ctrlBuffer.indexOf('[[');
          var emitText = ctrlBuffer;
          if (unsafeMarkerPos !== -1) {
            emitText = ctrlBuffer.slice(0, unsafeMarkerPos);
            ctrlBuffer = ctrlBuffer.slice(unsafeMarkerPos);
          } else {
            ctrlBuffer = '';
          }

          return emitText;
        }

        function applySummaryUpdateIfNeeded() {
          if (matchMeta) return Promise.resolve();
          if (!summaryUpdated) return Promise.resolve();
          if (summaryMeta && summaryMeta.project_detail) {
            upsertSummaryMessage(summaryMeta.project_detail, summaryMeta.project_name || '', true);
            renderMessages();
            return Promise.resolve();
          }

          return fetchLatestSummary(currentProjectId).then(function (latest) {
            if (latest && latest.project_detail) {
              upsertSummaryMessage(latest.project_detail, latest.project_name || '', true);
              renderMessages();
            }
          });
        }

        function finishStream() {
          assistantMsg.streaming = false;
          if (matchMeta) {
            assistantMsg.matchMeta = matchMeta;
          }
          renderMessages();
          return applySummaryUpdateIfNeeded();
        }

        function typeAppendText(text) {
          var arr = Array.from(text || '');
          if (!arr.length) return Promise.resolve();
          var idx = 0;
          return new Promise(function (resolve) {
            function step() {
              if (idx >= arr.length) {
                resolve();
                return;
              }
              assistantMsg.raw += arr[idx++];
              renderMessages();
              setTimeout(step, 10);
            }
            step();
          });
        }

        return fetch('./client_dashboard.php?view=create-project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
          .then(function (res) {
            if (!res.ok) {
              return res.text().then(function (errText) {
                var cleanErr = consumeChunk(errText || '');
                if (cleanErr) {
                  return typeAppendText(cleanErr).then(finishStream);
                }
                throw new Error('stream_failed');
              });
            }

            // Fallback path for browsers/environments without ReadableStream body.
            if (!res.body || !res.body.getReader) {
              return res.text().then(function (allText) {
                var clean = consumeChunk(allText || '');
                return typeAppendText(clean).then(finishStream);
              });
            }

            var reader = res.body.getReader();
            var decoder = new TextDecoder();

            function readChunk() {
              return reader.read().then(function (result) {
                if (result.done) {
                  var tail = consumeChunk('');
                  if (tail) {
                    assistantMsg.raw += tail;
                  }
                  return finishStream();
                }

                var chunk = decoder.decode(result.value, { stream: true });
                if (chunk) {
                  var textPart = consumeChunk(chunk);
                  if (textPart) {
                    assistantMsg.raw += textPart;
                  }
                  renderMessages();
                }
                return readChunk();
              });
            }

            return readChunk();
          })
          .catch(function () {
            assistantMsg.streaming = false;
            assistantMsg.raw = '<?= htmlspecialchars($isZhTw ? '系統暫時無法回覆，請稍後再試。' : 'The system cannot reply right now. Please try again shortly.', ENT_QUOTES, 'UTF-8') ?>';
            renderMessages();
          })
          .finally(function () {
            setChatBusy(false);
          });
      }

      function triggerInitialAi(projectId) {
        if (!projectId) return Promise.resolve();
        return streamAssistant({ action: 'ai_project_init_stream', project_id: projectId });
      }

      function decodeBase64Utf8Safe(b64) {
        try {
          var bin = atob(String(b64 || ''));
          var bytes = Uint8Array.from(bin, function (c) { return c.charCodeAt(0); });
          return new TextDecoder('utf-8').decode(bytes);
        } catch (e) {
          return '';
        }
      }

      function parseStoredAssistantContent(content) {
        var raw = String(content || '');
        var matchMeta = null;
        var prefix = '[[NJ_MATCH_META_BASE64]]';
        var suffix = '[[/NJ_MATCH_META_BASE64]]';

        while (true) {
          var s = raw.indexOf(prefix);
          if (s === -1) break;
          var e = raw.indexOf(suffix, s);
          if (e === -1) break;

          var b64 = raw.slice(s + prefix.length, e);
          var jsonText = decodeBase64Utf8Safe(b64);
          if (jsonText) {
            try { matchMeta = JSON.parse(jsonText); } catch (err) {}
          }

          raw = raw.slice(0, s) + raw.slice(e + suffix.length);
        }

        return { raw: raw, matchMeta: matchMeta };
      }

      function loadProject(projectId) {
        if (!projectId) return Promise.resolve();
        return fetch('./client_dashboard.php?view=create-project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'get_project', project_id: projectId })
        })
          .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('project_failed')); })
          .then(function (res) {
            if (!res || !res.ok || !res.project) throw new Error('project_failed');
            var project = res.project;
            currentProjectId = Number(project.id || 0) || null;
            updateNavbarProjectTitle(project.project_name || '');

            var detail = (project.project_detail && typeof project.project_detail === 'object') ? project.project_detail : {};
            var history = Array.isArray(project.jai_history) ? project.jai_history : [];

            messages = [];
            upsertSummaryMessage(detail, project.project_name || '', false);
            history.forEach(function (item) {
              var role = item && item.role;
              var content = item && item.content;
              if ((role === 'assistant' || role === 'user') && typeof content === 'string' && content.trim() !== '') {
                if (role === 'assistant') {
                  var parsedMsg = parseStoredAssistantContent(content);
                  var msg = { role: 'assistant', raw: parsedMsg.raw || '' };
                  if (parsedMsg.matchMeta) {
                    msg.matchMeta = parsedMsg.matchMeta;
                  }
                  messages.push(msg);
                } else {
                  messages.push({ role: 'user', raw: content });
                }
              }
            });
            renderMessages();
          })
          .catch(function () {});
      }

      function updateNavbarProjectTitle(name) {
        if (!navbarProjectTitle) return;
        if (!isCreateProjectView) {
          navbarProjectTitle.textContent = '';
          return;
        }
        var nextName = (name || '').trim();
        navbarProjectTitle.textContent = nextName !== '' ? nextName : unnamedProject;
      }

      function setError(msg) {
        if (!msg) {
          errorBox.classList.add('hidden');
          errorBox.textContent = '';
          return;
        }
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
      }

      function showStep(n) {
        currentStep = n;
        steps.forEach(function (el) { el.classList.toggle('hidden', Number(el.dataset.step) !== n); });
        stepText.textContent = '<?= htmlspecialchars($t['step'], ENT_QUOTES, 'UTF-8') ?> ' + n + ' / 3';
        prevBtn.disabled = n === 1;
        prevBtn.classList.toggle('hidden', n === 1);
        nextBtn.classList.toggle('hidden', n === 3);
        submitBtn.classList.toggle('hidden', n !== 3);
        setError('');
      }

      function validateStep(n) {
        if (n === 1) {
          if (!document.querySelector('input[name="project_mode"]:checked')) {
            setError('<?= htmlspecialchars($t['validation_mode'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }
          return true;
        }

        if (n === 2) {
          if (!document.querySelector('input[name="project_types[]"]:checked')) {
            setError('<?= htmlspecialchars($t['validation_type'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }
          if (!document.querySelector('input[name="stacks[]"]:checked')) {
            setError('<?= htmlspecialchars($t['validation_stack'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }
          return true;
        }

        if (n === 3) {
          var projectName = (document.getElementById('project-name-input').value || '').trim();
          if (projectName.length < 3) {
            setError('<?= htmlspecialchars($t['validation_project_name'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }

          var budgetRaw = (document.getElementById('budget-input').value || '').replace(/,/g, '').trim();
          var budgetNum = Number(budgetRaw);
          if (!/^\d+(\.\d+)?$/.test(budgetRaw) || !Number.isFinite(budgetNum) || budgetNum <= 0) {
            setError('<?= htmlspecialchars($t['validation_budget'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }
          if (!document.querySelector('input[name="accept_foreign"]:checked')) {
            setError('<?= htmlspecialchars($t['validation_foreign'], ENT_QUOTES, 'UTF-8') ?>');
            return false;
          }
          return true;
        }
        return true;
      }

      function collectProjectData() {
        return {
          mode: document.querySelector('input[name="project_mode"]:checked')?.value || '',
          types: Array.from(document.querySelectorAll('input[name="project_types[]"]:checked')).map(function (x) { return x.value; }),
          stacks: Array.from(document.querySelectorAll('input[name="stacks[]"]:checked')).map(function (x) { return x.value; }),
          project_name: (document.getElementById('project-name-input').value || '').trim(),
          budget: (document.getElementById('budget-input').value || '').trim(),
          accept_foreign: document.querySelector('input[name="accept_foreign"]:checked')?.value || '',
          ai_description: ''
        };
      }

      function saveProjectDraft(completed) {
        var payload = collectProjectData();
        payload.action = 'save_project';
        payload.completed = !!completed;
        payload.project_id = currentProjectId || 0;

        return fetch('./client_dashboard.php?view=create-project', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
          .then(function (res) { return res.ok ? res.json() : Promise.reject(new Error('save_failed')); })
          .then(function (res) {
            if (!res || !res.ok) throw new Error('save_failed');
            currentProjectId = Number(res.project_id || 0) || currentProjectId;
            updateNavbarProjectTitle(res.project_name || payload.project_name || '');
            return res;
          })
          .catch(function () {
            updateNavbarProjectTitle(payload.project_name || '');
            return null;
          });
      }

      function closeModal(completed) {
        saveProjectDraft(!!completed).then(function (res) {
          var pid = res && res.project_id ? Number(res.project_id) : currentProjectId;
          if (pid) {
            currentProjectId = pid;
            if (res && res.project_detail) {
              upsertSummaryMessage(res.project_detail, (res.project_name || ''), false);
              renderMessages();
            }
            triggerInitialAi(pid).then(function () {
              loadProjectList();
            });
          } else {
            loadProjectList();
          }
        });
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        setError('');
      }

      closeBtn && closeBtn.addEventListener('click', function () { closeModal(false); });
      modal && modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(false); });
      prevBtn && prevBtn.addEventListener('click', function () { if (currentStep > 1) showStep(currentStep - 1); });
      nextBtn && nextBtn.addEventListener('click', function () {
        if (!validateStep(currentStep)) return;
        if (currentStep < 3) showStep(currentStep + 1);
      });

      var chatMessages = document.getElementById('chat-messages');
      var chatForm = document.getElementById('chat-form');
      var chatInput = document.getElementById('chat-input');
      var askPlaceholderDefault = '<?= htmlspecialchars($t['ask_placeholder'], ENT_QUOTES, 'UTF-8') ?>';
      var askPlaceholderRunning = '<?= htmlspecialchars($t['ask_placeholder_running'], ENT_QUOTES, 'UTF-8') ?>';

      var messages = [];

      function setChatBusy(isBusy) {
        if (!chatInput) return;
        chatInput.disabled = !!isBusy;
        if (isBusy) {
          chatInput.value = '';
          chatInput.placeholder = askPlaceholderRunning;
        } else {
          chatInput.placeholder = askPlaceholderDefault;
        }
      }

      function renderMessages() {
        if (!chatMessages) return;
        chatMessages.innerHTML = '';

        messages.forEach(function (m, idx) {
          var row = document.createElement('div');
          row.className = m.role === 'user' ? 'flex justify-end' : 'group';

          if (m.role === 'user') {
            var bubble = document.createElement('div');
            bubble.className = 'max-w-[85%] rounded-2xl bg-white px-4 py-3 text-sm dark:bg-zinc-900';
            bubble.textContent = m.raw;
            row.appendChild(bubble);
          } else {
            var box = document.createElement('div');
            var isMatchCard = !!m.matchMeta;
            box.className = m.systemIntro
              ? 'rounded-2xl bg-[#7e6ea2] p-4 text-white'
              : (isMatchCard ? 'rounded-2xl bg-[#437c82] p-4 text-white' : 'rounded-2xl bg-white/80 p-4 dark:bg-zinc-900/60');

            if (isMatchCard) {
              var title = document.createElement('p');
              title.className = 'text-sm sm:text-base font-semibold';
              title.textContent = (m.matchMeta && m.matchMeta.title) ? m.matchMeta.title : 'Matching engineer candidates found';
              box.appendChild(title);
              var list = Array.isArray(m.matchMeta.items) ? m.matchMeta.items : [];
              list.forEach(function (it) {
                var card = document.createElement('div');
                card.className = 'mt-3 rounded-xl bg-white/10 p-3';

                var body = document.createElement('div');
                body.className = 'flex gap-3';

                var left = document.createElement('div');
                left.className = 'shrink-0';
                var avatar = document.createElement('img');
                avatar.className = 'h-14 w-14 rounded-full object-cover ring-2 ring-white/35';
                avatar.src = it.avatar_url || '';
                avatar.alt = it.name || 'avatar';
                left.appendChild(avatar);

                var right = document.createElement('div');
                right.className = 'min-w-0 flex-1 text-sm leading-6';

                function addLine(label, value, asLink) {
                  var v = (value || '').toString().trim();
                  if (!v) return;
                  var p = document.createElement('p');
                  var strong = document.createElement('span');
                  strong.className = 'font-semibold';
                  strong.textContent = label + ': ';
                  p.appendChild(strong);
                  if (asLink) {
                    var a = document.createElement('a');
                    a.href = asLink;
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.className = 'underline';
                    a.textContent = v;
                    p.appendChild(a);
                  } else {
                    p.appendChild(document.createTextNode(v));
                  }
                  right.appendChild(p);
                }
                var nameTitle = document.createElement('p');
                nameTitle.className = 'text-lg font-bold leading-6';
                nameTitle.textContent = (it.name || '-');
                right.appendChild(nameTitle);

                var repoP = document.createElement('p');
                var repoNameText = (it.repo_name || '-').toString().trim() || '-';
                repoP.appendChild(document.createTextNode(repoNameText + ': '));
                var repoA = document.createElement('a');
                repoA.href = it.repo_url || '#';
                repoA.target = '_blank';
                repoA.rel = 'noopener noreferrer';
                repoA.className = 'underline';
                repoA.textContent = it.repo_url || '-';
                repoP.appendChild(repoA);
                right.appendChild(repoP);

                var tags = Array.isArray(it.tags) ? it.tags.filter(function (x) { return (x || '').toString().trim() !== ''; }) : [];
                if (tags.length) {
                  var tagWrap = document.createElement('div');
                  tagWrap.className = 'mt-1 flex flex-wrap gap-1.5';
                  tags.forEach(function (tag) {
                    var chip = document.createElement('span');
                    chip.className = 'inline-flex rounded-full border border-white/40 bg-white/15 px-2 py-0.5 text-xs';
                    chip.textContent = tag;
                    tagWrap.appendChild(chip);
                  });
                  right.appendChild(tagWrap);
                }

                var sep1 = document.createElement('div');
                sep1.className = 'my-2 h-px bg-white/35';
                right.appendChild(sep1);

                var scoreNum = Number(it.score || 0);
                addLine(matchLabels.score, String(Math.round(scoreNum)) + '%');
                var reasonText = (it.reason || '').toString().trim();
                reasonText = reasonText
                  .replace(/^\s*Repo\s*簡介\s*[:：]\s*/i, '')
                  .replace(/^\s*Repo\s*brief\s*[:：]\s*/i, '');
                if (reasonText) {
                  var reasonP = document.createElement('p');
                  reasonP.className = 'mt-1 whitespace-pre-line text-white/95';
                  reasonP.textContent = reasonText;
                  right.appendChild(reasonP);
                }

                var sep2 = document.createElement('div');
                sep2.className = 'my-2 h-px bg-white/35';
                right.appendChild(sep2);

                addLine(matchLabels.country, it.country || '');
                addLine(matchLabels.web, it.web_url || '', it.web_url || '');
                addLine(matchLabels.linkedin, it.linkedin_url || '', it.linkedin_url || '');
                addLine(matchLabels.email, it.email || '', (it.email ? ('mailto:' + it.email) : ''));
                addLine(matchLabels.phone, it.phone_display || '', (it.phone_tel ? ('tel:' + it.phone_tel) : ''));
                addLine(matchLabels.whatsapp, it.whatsapp || '');
                addLine(matchLabels.line, it.line_id || '');

                body.appendChild(left);
                body.appendChild(right);
                card.appendChild(body);
                box.appendChild(card);
              });
            } else {
              var md = document.createElement('div');
              md.className = 'md-content text-sm sm:text-base';
              md.innerHTML = DOMPurify.sanitize(marked.parse(m.raw || ''));
              if (m.streaming && (!m.raw || m.raw.trim() === '')) {
                md.textContent = ' ';
              }
              if (m.streaming) {
                var cursor = document.createElement('span');
                cursor.className = 'typing-cursor';
                cursor.textContent = '▍';
                md.appendChild(cursor);
              }
              box.appendChild(md);
            }

            var actions = document.createElement('div');
            actions.className = 'mt-3 flex justify-end';
            var copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'rounded-lg border border-zinc-300 px-2.5 py-1 text-xs text-zinc-600 opacity-0 transition group-hover:opacity-100 dark:border-zinc-700 dark:text-zinc-300';
            if (isMatchCard) {
              copyBtn.className = 'rounded-lg border border-white/45 px-2.5 py-1 text-xs text-white opacity-90 transition group-hover:opacity-100';
            }
            copyBtn.textContent = '<?= htmlspecialchars($t['copy'], ENT_QUOTES, 'UTF-8') ?>';
            copyBtn.addEventListener('click', function () {
              navigator.clipboard.writeText(m.raw || '');
              copyBtn.textContent = '<?= htmlspecialchars($t['copied'], ENT_QUOTES, 'UTF-8') ?>';
              setTimeout(function () { copyBtn.textContent = '<?= htmlspecialchars($t['copy'], ENT_QUOTES, 'UTF-8') ?>'; }, 1200);
            });
            actions.appendChild(copyBtn);
            box.appendChild(actions);

            row.appendChild(box);
          }

          chatMessages.appendChild(row);
        });

        chatMessages.scrollTop = chatMessages.scrollHeight;
      }

      function addAssistantSummary(data) {
        var md = [
          '## Project Draft',
          '',
          '- **Mode:** ' + data.mode,
          '- **Types:** ' + (data.types.length ? data.types.join(', ') : '-'),
          '- **Stack:** ' + (data.stacks.length ? data.stacks.join(', ') : '-'),
          '- **Project Name:** ' + data.projectName,
          '- **Budget:** ' + (data.budget ? (data.budget + ' USD') : '-'),
          '- **Overseas Engineers:** ' + data.acceptForeign,
          '- **AI Description:** ' + (data.ai_description || ''),
          '',
          '```json',
          JSON.stringify(data, null, 2),
          '```'
        ].join('\n');
        messages.push({ role: 'assistant', raw: md });
      }

      submitBtn && submitBtn.addEventListener('click', function () {
        if (!validateStep(3)) return;

        closeModal(true);
        document.getElementById('project-form').reset();
      });

      if (chatForm && chatInput) {
        var isComposing = false;

        chatForm.addEventListener('submit', function (e) {
          e.preventDefault();
          var text = chatInput.value.trim();
          if (!text) return;
          if (!currentProjectId) return;
          if (chatInput.disabled) return;

          messages.push({ role: 'user', raw: text });
          chatInput.value = '';
          renderMessages();

          streamAssistant({ action: 'ai_chat_user_stream', project_id: currentProjectId, message: text });
        });

        chatInput.addEventListener('compositionstart', function () {
          isComposing = true;
        });

        chatInput.addEventListener('compositionend', function () {
          isComposing = false;
        });

        chatInput.addEventListener('keydown', function (e) {
          var native = e || window.event;
          if (native.isComposing || isComposing || native.keyCode === 229) {
            return;
          }

          if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.requestSubmit();
          }
        });
      }

      loadProjectList();

      if ('<?= $view ?>' === 'create-project') {
        if (selectedProjectId > 0) {
          loadProject(selectedProjectId);
        } else {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          showStep(1);

          messages = messages.filter(function (m) {
            return !(m.role === 'assistant' && /完成專案基本問題|需求摘要/.test(m.raw || ''));
          });
          renderMessages();
        }
      }
    })();
  </script>
</body>
</html>
