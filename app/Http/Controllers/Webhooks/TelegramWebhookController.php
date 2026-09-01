<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Notifications\CommandRouter;
use App\Services\Notifications\TelegramClient;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, CommandRouter $router, TelegramClient $telegram)
    {
        $expected = (string) config('notifications.telegram.webhook_secret');
        if ($expected === '' || ! hash_equals($expected, $secret)) {
            abort(404);
        }

        $update = $request->all();
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';
        if (! $chatId || $text === '') {
            return response()->json(['ok' => true]);
        }

        $reply = $router->handle($text, 'telegram');
        if ($reply !== null) {
            $telegram->sendTo($chatId, $reply);
        }

        return response()->json(['ok' => true]);
    }
}
