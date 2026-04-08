#!/usr/bin/env php
<?php

// Quick Telegram Webhook Setup Script
// Run: php setup_telegram.php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['TELEGRAM_BOT_TOKEN'];
$appUrl = $_ENV['APP_URL'];
$webhookUrl = $appUrl . '/telegram/webhook';

echo "========================================\n";
echo "Telegram Webhook Setup\n";
echo "========================================\n";
echo "Bot Token: " . substr($token, 0, 10) . "...\n";
echo "App URL: $appUrl\n";
echo "Webhook URL: $webhookUrl\n";
echo "========================================\n\n";

// Delete old webhook first
echo "[1/3] Deleting old webhook...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/deleteWebhook");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);
echo "Result: " . ($data['ok'] ? "✅ Success" : "❌ Failed") . "\n\n";

// Set new webhook
echo "[2/3] Setting new webhook...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/setWebhook");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['url' => $webhookUrl]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);
echo "Result: " . ($data['ok'] ? "✅ Success" : "❌ Failed - " . $data['description']) . "\n\n";

// Check webhook status
echo "[3/3] Checking webhook status...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$token}/getWebhookInfo");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);

if ($data['ok']) {
    $info = $data['result'];
    echo "✅ Webhook URL: " . $info['url'] . "\n";
    echo "Has Custom Certificate: " . ($info['has_custom_certificate'] ? "Yes" : "No") . "\n";
    echo "Pending Update Count: " . $info['pending_update_count'] . "\n";
} else {
    echo "❌ Failed to get webhook info\n";
}

echo "\n========================================\n";
echo "Setup Complete!\n";
echo "========================================\n";
?>
