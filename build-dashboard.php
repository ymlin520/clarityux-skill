<?php
declare(strict_types=1);

$configFile = __DIR__ . '/config.php';
$exampleConfigFile = __DIR__ . '/config.example.php';
$config = file_exists($configFile) ? require $configFile : require $exampleConfigFile;

$templatePath = __DIR__ . '/templates/standalone-clarity-dashboard.html';
$outputDir = __DIR__ . '/dist';
$outputFile = $outputDir . '/dashboard.html';

if (!file_exists($templatePath)) {
    fwrite(STDERR, "Template not found: {$templatePath}\n");
    exit(1);
}

$template = file_get_contents($templatePath);
if ($template === false) {
    fwrite(STDERR, "Unable to read template.\n");
    exit(1);
}

$apiEndpoint = (string)($config['public_api_endpoint'] ?? '/api.php');
$siteLabel = (string)($config['public_site_label'] ?? ($config['site'] ?? ''));
$title = (string)($config['dashboard_title'] ?? '網站 UX 行為洞察儀表板');

$rendered = str_replace(
    ['{{DASHBOARD_TITLE}}', '{{API_ENDPOINT}}', '{{SITE_LABEL}}'],
    [htmlspecialchars($title, ENT_QUOTES), htmlspecialchars($apiEndpoint, ENT_QUOTES), htmlspecialchars($siteLabel, ENT_QUOTES)],
    $template
);

if (!is_dir($outputDir) && !mkdir($outputDir, 0775, true) && !is_dir($outputDir)) {
    fwrite(STDERR, "Unable to create dist directory.\n");
    exit(1);
}

if (file_put_contents($outputFile, $rendered) === false) {
    fwrite(STDERR, "Unable to write {$outputFile}.\n");
    exit(1);
}

fwrite(STDOUT, "BUILT {$outputFile}\n");
