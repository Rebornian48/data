<?php

namespace App\Console\Commands;

use App\Services\Notifications\MemberEventNotifier;
use Illuminate\Console\Command;

class SendDailyNotifications extends Command
{
    protected $signature = 'notifications:daily {--date= : Override date (YYYY-MM-DD) for backfill / testing}';

    protected $description = 'Broadcast birthday & graduation events for the given day to Telegram + Discord.';

    public function handle(MemberEventNotifier $notifier): int
    {
        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date')) : now();
        $result = $notifier->runDaily($date);
        $this->info("Sent: birthday={$result['birthday']} graduation={$result['graduation']} on {$date->toDateString()}");

        return self::SUCCESS;
    }
}
