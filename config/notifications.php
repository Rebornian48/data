<?php

return [

    'telegram' => [
        'enabled' => (bool) env('TELEGRAM_ENABLED', false),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_ids' => array_values(array_filter(array_map('trim', explode(',', (string) env('TELEGRAM_CHAT_IDS', ''))))),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'parse_mode' => env('TELEGRAM_PARSE_MODE', 'HTML'),
    ],

    'discord' => [
        'enabled' => (bool) env('DISCORD_ENABLED', false),
        'webhook_urls' => array_values(array_filter(array_map('trim', explode(',', (string) env('DISCORD_WEBHOOK_URLS', ''))))),
        'application_id' => env('DISCORD_APPLICATION_ID'),
        'public_key' => env('DISCORD_PUBLIC_KEY'),
        'bot_token' => env('DISCORD_BOT_TOKEN'),
    ],

    'daily_run_time' => env('NOTIFICATIONS_DAILY_TIME', '08:00'),

];
