<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$configFile = __DIR__ . '/config.php';
$exampleConfigFile = __DIR__ . '/config.example.php';
$config = file_exists($configFile) ? require $configFile : require $exampleConfigFile;

$allowOrigin = $config['cors_allow_origin'] ?? '*';
header('Access-Control-Allow-Origin: ' . $allowOrigin);
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    guardAccess($config);
    $payload = buildPayload($config);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code((int)($e->getCode() >= 400 ? $e->getCode() : 500));
    echo json_encode([
        'status' => 'error',
        'statusLabel' => '載入失敗',
        'statusMessage' => $e->getMessage(),
        'site' => $config['site'] ?? '',
        'projectId' => $config['project_id'] ?? '',
        'rangeLabel' => '未知',
        'summaryCards' => [],
        'issueCards' => [],
        'charts' => [
            'labels' => [],
            'sessions' => [],
            'rageClicks' => [],
            'deadClicks' => [],
            'quickBacks' => [],
            'scrollDepth' => [],
        ],
        'problemPages' => [],
        'recordings' => [],
        'segments' => [
            'devices' => [],
            'sources' => [],
            'landingPages' => [],
        ],
        'recommendations' => [],
        'alerts' => [
            [
                'tone' => 'warn',
                'message' => 'API 錯誤：' . $e->getMessage(),
            ],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function guardAccess(array $config): void
{
    $expected = trim((string)($config['access_token'] ?? ''));
    if ($expected === '') {
        return;
    }

    $provided = (string)($_GET['access_token'] ?? '');
    if (!hash_equals($expected, $provided)) {
        throw new RuntimeException('Unauthorized', 401);
    }
}

function buildPayload(array $config): array
{
    $mode = (string)($config['mode'] ?? 'mock');

    return match ($mode) {
        'mock' => enrichPayload(loadJsonFile(__DIR__ . '/examples/payload.example.json'), $config),
        'file' => enrichPayload(loadJsonFile((string)($config['payload_file'] ?? '')), $config),
        'remote_json' => enrichPayload(loadRemoteJson($config), $config),
        default => throw new RuntimeException('Unsupported mode: ' . $mode, 500),
    };
}

function loadJsonFile(string $path): array
{
    if ($path === '' || !file_exists($path)) {
        throw new RuntimeException('JSON payload file not found: ' . $path, 500);
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('Unable to read JSON payload file.', 500);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('Invalid JSON payload file.', 500);
    }

    return $data;
}

function loadRemoteJson(array $config): array
{
    $baseUrl = trim((string)($config['remote_json_url'] ?? ''));
    if ($baseUrl === '') {
        throw new RuntimeException('remote_json_url is empty.', 500);
    }

    $query = $config['remote_query'] ?? [];
    if (!is_array($query)) {
        $query = [];
    }

    $url = $baseUrl;
    if ($query !== []) {
        $qs = http_build_query($query);
        $url .= (str_contains($baseUrl, '?') ? '&' : '?') . $qs;
    }

    $headers = [
        'Accept: application/json',
        'User-Agent: ClarityUxInsightsSkill/1.0',
    ];

    $bearer = trim((string)($config['remote_bearer_token'] ?? ''));
    if ($bearer !== '') {
        $headers[] = 'Authorization: Bearer ' . $bearer;
    }

    foreach (($config['remote_headers'] ?? []) as $header) {
        if (is_string($header) && trim($header) !== '') {
            $headers[] = $header;
        }
    }

    $method = strtoupper((string)($config['remote_method'] ?? 'GET'));
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'timeout' => 25,
            'ignore_errors' => true,
        ],
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false) {
        throw new RuntimeException('Unable to fetch remote JSON.', 502);
    }

    $status = detectHttpStatus($http_response_header ?? []);
    if ($status >= 400) {
        throw new RuntimeException('Remote JSON API returned HTTP ' . $status, 502);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('Remote JSON API did not return valid JSON.', 502);
    }

    return $data;
}

function detectHttpStatus(array $headers): int
{
    foreach ($headers as $line) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) {
            return (int)$m[1];
        }
    }
    return 200;
}

function enrichPayload(array $payload, array $config): array
{
    $payload['dashboardTitle'] = (string)($config['dashboard_title'] ?? ($payload['dashboardTitle'] ?? '網站 UX 行為洞察儀表板'));
    $payload['site'] = (string)($config['site'] ?? ($payload['site'] ?? ''));
    $payload['projectId'] = (string)($config['project_id'] ?? ($payload['projectId'] ?? ''));

    $payload['status'] = (string)($payload['status'] ?? 'ok');
    $payload['statusLabel'] = (string)($payload['statusLabel'] ?? '資料已同步');
    $payload['statusMessage'] = (string)($payload['statusMessage'] ?? 'Clarity UX 行為資料已完成更新');
    $payload['rangeLabel'] = (string)($payload['rangeLabel'] ?? '近 7 天');

    $payload['summaryCards'] = ensureArray($payload['summaryCards'] ?? []);
    $payload['issueCards'] = ensureArray($payload['issueCards'] ?? []);
    $payload['charts'] = is_array($payload['charts'] ?? null) ? $payload['charts'] : [];
    $payload['problemPages'] = ensureArray($payload['problemPages'] ?? []);
    $payload['recordings'] = ensureArray($payload['recordings'] ?? []);
    $payload['segments'] = is_array($payload['segments'] ?? null) ? $payload['segments'] : [
        'devices' => [],
        'sources' => [],
        'landingPages' => [],
    ];
    $payload['recommendations'] = ensureArray($payload['recommendations'] ?? []);
    $payload['alerts'] = ensureArray($payload['alerts'] ?? []);

    return $payload;
}

function ensureArray(mixed $value): array
{
    return is_array($value) ? array_values($value) : [];
}
