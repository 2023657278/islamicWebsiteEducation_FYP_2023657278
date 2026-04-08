<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;

    public function __construct()
    {
        // Use the config, or fallback to env directly
        $this->token = config('services.telegram-bot-api.token', env('TELEGRAM_BOT_TOKEN'));
    }

    // 1. Send Message to a specific User
    public function sendMessage($chatId, $message)
    {
        if (!$chatId) return false;

        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        try {
            $response = Http::withoutVerifying()->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram Send Error: " . $e->getMessage());
            return false;
        }
    }

    // 2. Check for Updates (To link accounts)
    public function getUpdates()
    {
        // We add ?allowed_updates=["message"] to filter out clutter
        $url = "https://api.telegram.org/bot{$this->token}/getUpdates?allowed_updates=[\"message\"]";

        try {
            $response = Http::withoutVerifying()->get($url);
            
            if ($response->successful()) {
                // Log the raw response for debugging (Check storage/logs/laravel.log)
                Log::info('Telegram Updates Raw:', $response->json());
                return $response->json();
            }
            
            Log::error('Telegram API Failed:', $response->json());
            return [];
        } catch (\Exception $e) {
            Log::error("Telegram Connection Error: " . $e->getMessage());
            return [];
        }
    }
}