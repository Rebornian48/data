<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Notifications\CommandRouter;
use Illuminate\Http\Request;

class DiscordWebhookController extends Controller
{
    // Discord interaction types
    private const PING = 1;
    private const APPLICATION_COMMAND = 2;

    public function __invoke(Request $request, CommandRouter $router)
    {
        $publicKey = (string) config('notifications.discord.public_key');
        if ($publicKey === '' || ! $this->verifySignature($request, $publicKey)) {
            abort(401, 'invalid request signature');
        }

        $data = $request->all();
        $type = $data['type'] ?? 0;

        if ($type === self::PING) {
            return response()->json(['type' => 1]);
        }

        if ($type === self::APPLICATION_COMMAND) {
            $name = $data['data']['name'] ?? '';
            $args = '';
            foreach (($data['data']['options'] ?? []) as $opt) {
                $args .= ' '.($opt['value'] ?? '');
            }
            $reply = $router->handle('/'.$name.$args, 'discord') ?? 'Command tidak dikenali.';

            return response()->json([
                'type' => 4,
                'data' => ['content' => $reply],
            ]);
        }

        return response()->json(['type' => 1]);
    }

    private function verifySignature(Request $request, string $publicKeyHex): bool
    {
        $signature = $request->header('X-Signature-Ed25519');
        $timestamp = $request->header('X-Signature-Timestamp');
        if (! $signature || ! $timestamp) {
            return false;
        }
        if (! function_exists('sodium_crypto_sign_verify_detached')) {
            return false;
        }
        try {
            return sodium_crypto_sign_verify_detached(
                sodium_hex2bin($signature),
                $timestamp.$request->getContent(),
                sodium_hex2bin($publicKeyHex),
            );
        } catch (\Throwable) {
            return false;
        }
    }
}
