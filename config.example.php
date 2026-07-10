<?php
return [
    // 可選：mock / file / remote_json
    // mock: 直接讀 examples/payload.example.json
    // file: 讀你自己準備的本機 JSON 檔
    // remote_json: 從你自己的後端 bridge / Clarity 整理 API 抓 JSON
    'mode' => 'mock',

    // 站點顯示名稱
    'site' => 'https://example.com/',

    // 你的 Clarity Project ID（先做顯示與傳遞用途）
    'project_id' => 'clarity-project-demo',

    // 儀表板標題
    'dashboard_title' => '網站 UX 行為洞察儀表板',

    // ===== mode=file 時使用 =====
    'payload_file' => __DIR__ . '/examples/payload.example.json',

    // ===== mode=remote_json 時使用 =====
    'remote_json_url' => 'https://example.com/api/clarity-bridge',
    'remote_method'   => 'GET',

    // 若你的 bridge API 需要 Bearer Token，就填這裡
    'remote_bearer_token' => '',

    // 額外 Query 參數，可帶 project/site/range 等給你的 bridge
    'remote_query' => [
        // 'projectId' => 'clarity-project-demo',
        // 'range' => '7d',
    ],

    // 額外 HTTP headers
    'remote_headers' => [
        // 'X-Api-Key: your-key',
    ],

    // 基本保護：若你不想讓任何人都打這支 api.php，可填入一組 token
    // 前端呼叫時需帶 ?access_token=這裡的值
    'access_token' => '',

    // 是否允許跨網域讀取
    'cors_allow_origin' => '*',
];
