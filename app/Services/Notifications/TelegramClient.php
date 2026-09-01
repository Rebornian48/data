<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramClient
{
    public function enabled(): bool
    {
        return (bool) config('notifications.telegram.enabled')
            && filled(config('notifications.telegram.bot_token'));
    }

    public function chatIds(): array
    {
        return (array) config('notifications.telegram.chat_ids', []);
    }

    public function broadcast(string $text): int
    {
        if (! $this->enabled()) {
            return 0;
        }
        $sent = 0;
        foreach ($this->chatIds() as $chatId) {
            if ($this->sendTo($chatId, $text)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendTo(string|int $chatId, string $text): bool
    {
        if (! $this->enabled()) {
            return false;
        }
        $token = config('notifications.telegram.bot_token');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        try {
            $resp = Http::asJson()->timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => config('notifications.telegram.parse_mode', 'HTML'),
                'disable_web_page_preview' => true,
            ]);
            if (! $resp->ok()) {
                Log::warning('Telegram send failed', ['chat_id' => $chatId, 'body' => $resp->body()]);
            }

            return $resp->ok();
        } catch (\Throwable $e) {
            Log::error('Telegram exception', ['msg' => $e->getMessage()]);

            return false;
        }
    }
}
