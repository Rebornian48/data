<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordClient
{
    public function enabled(): bool
    {
        return (bool) config('notifications.discord.enabled')
            && count($this->webhookUrls()) > 0;
    }

    public function webhookUrls(): array
    {
        return (array) config('notifications.discord.webhook_urls', []);
    }

    public function broadcast(string $content, array $embeds = []): int
    {
        if (! $this->enabled()) {
            return 0;
        }
        $sent = 0;
        foreach ($this->webhookUrls() as $url) {
            if ($this->sendWebhook($url, $content, $embeds)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendWebhook(string $url, string $content, array $embeds = []): bool
    {
        $payload = ['content' => $content];
        if ($embeds) {
            $payload['embeds'] = $embeds;
        }
        try {
            $resp = Http::asJson()->timeout(10)->post($url, $payload);
            if (! $resp->successful()) {
                Log::warning('Discord webhook failed', ['status' => $resp->status(), 'body' => $resp->body()]);
            }

            return $resp->successful();
        } catch (\Throwable $e) {
            Log::error('Discord exception', ['msg' => $e->getMessage()]);

            return false;
        }
    }
}
