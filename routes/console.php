<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('members:sync-status')->dailyAt('00:05')->timezone('Asia/Jakarta');
Schedule::command('notifications:daily')
    ->dailyAt(config('notifications.daily_run_time', '08:00'))
    ->timezone('Asia/Jakarta');
