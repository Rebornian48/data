<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class SyncMemberStatus extends Command
{
    protected $signature = 'members:sync-status';

    protected $description = 'Flip status to Lulus for any member whose graduation_date has arrived or passed.';

    public function handle(): int
    {
        $today = now()->toDateString();

        $count = Member::where('status', 'Aktif')
            ->whereNotNull('graduation_date')
            ->whereDate('graduation_date', '<=', $today)
            ->update(['status' => 'Lulus']);

        $this->info("Updated {$count} member(s) to Lulus.");

        return self::SUCCESS;
    }
}
