<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DocsController extends Controller
{
    public function api(): View
    {
        return view('admin.docs.api', [
            'docsUrl' => route('api.docs'),
            'specUrl' => route('api.docs.spec'),
            'baseUrl' => url('/api'),
        ]);
    }

    public function telegram(): View
    {
        $webhookSecret = (string) config('notifications.telegram.webhook_secret');
        $webhookUrl = $webhookSecret !== ''
            ? url('/webhooks/telegram/'.$webhookSecret)
            : url('/webhooks/telegram/<TELEGRAM_WEBHOOK_SECRET>');

        return view('admin.docs.telegram', [
            'enabled'       => (bool) config('notifications.telegram.enabled'),
            'hasBotToken'   => (bool) config('notifications.telegram.bot_token'),
            'hasSecret'     => $webhookSecret !== '',
            'chatIds'       => (array) config('notifications.telegram.chat_ids', []),
            'parseMode'     => (string) config('notifications.telegram.parse_mode', 'HTML'),
            'webhookUrl'    => $webhookUrl,
            'dailyTime'     => (string) config('notifications.daily_run_time', '08:00'),
        ]);
    }

    public function discord(): View
    {
        return view('admin.docs.discord', [
            'enabled'         => (bool) config('notifications.discord.enabled'),
            'hasApplicationId' => (bool) config('notifications.discord.application_id'),
            'hasPublicKey'    => (bool) config('notifications.discord.public_key'),
            'hasBotToken'     => (bool) config('notifications.discord.bot_token'),
            'webhookCount'    => count((array) config('notifications.discord.webhook_urls', [])),
            'interactionsUrl' => url('/webhooks/discord'),
            'dailyTime'       => (string) config('notifications.daily_run_time', '08:00'),
        ]);
    }
}
