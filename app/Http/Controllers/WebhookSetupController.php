<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookSetupController extends Controller
{
    // Setup Telegram Webhook
    // Visit: http://localhost:8000/setup-telegram-webhook
    public function setupTelegramWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $appUrl = env('APP_URL');
        $webhookUrl = $appUrl . '/telegram/webhook';

        Log::info('Setting up Telegram Webhook', [
            'token' => substr($token, 0, 10) . '...',
            'app_url' => $appUrl,
            'webhook_url' => $webhookUrl
        ]);

        try {
            $url = "https://api.telegram.org/bot{$token}/setWebhook";

            $response = Http::withoutVerifying()->post($url, [
                'url' => $webhookUrl,
            ]);

            $result = $response->json();

            return response()->json([
                'status' => $result['ok'] ? 'success' : 'failed',
                'message' => $result['description'] ?? 'No message',
                'webhook_url' => $webhookUrl,
                'full_response' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Webhook Setup Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Check Webhook Status
    // Visit: http://localhost:8000/check-telegram-webhook
    public function checkTelegramWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        try {
            $url = "https://api.telegram.org/bot{$token}/getWebhookInfo";

            $response = Http::withoutVerifying()->get($url);

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete Webhook
    // Visit: http://localhost:8000/delete-telegram-webhook
    public function deleteTelegramWebhook()
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        try {
            $url = "https://api.telegram.org/bot{$token}/deleteWebhook";

            $response = Http::withoutVerifying()->post($url);

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook deleted',
                'response' => $response->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
