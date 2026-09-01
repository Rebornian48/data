<?php

namespace App\Services\Notifications;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MemberEventNotifier
{
    public function __construct(
        private readonly TelegramClient $telegram,
        private readonly DiscordClient $discord,
    ) {}

    public function runDaily(?Carbon $date = null): array
    {
        $date = ($date ?? now())->copy()->startOfDay();
        $summary = [
            'birthday' => $this->dispatchBirthdays($date),
            'graduation' => $this->dispatchGraduations($date),
        ];

        return $summary;
    }

    private function dispatchBirthdays(Carbon $date): int
    {
        $members = Member::whereNotNull('birth_date')
            ->whereRaw('MONTH(birth_date) = ?', [$date->month])
            ->whereRaw('DAY(birth_date) = ?', [$date->day])
            ->where('status', 'Aktif')
            ->get();

        $count = 0;
        foreach ($members as $m) {
            $age = $date->year - $m->birth_date->year;
            $url = route('members.show', $m);
            $telegramMsg = "🎂 <b>Selamat ulang tahun ke-{$age}</b> untuk <b>{$m->name}</b>!\n"
                .($m->nickname ? "({$m->nickname})\n" : '')
                ."\n<a href=\"{$url}\">Profil member</a>";
            $discordMsg = "🎂 **Selamat ulang tahun ke-{$age}** untuk **{$m->name}**".($m->nickname ? " ({$m->nickname})" : '')."\n{$url}";
            $count += $this->send('birthday', $m->id, $date, $telegramMsg, $discordMsg, $m);
        }

        return $count;
    }

    private function dispatchGraduations(Carbon $date): int
    {
        $members = Member::whereNotNull('graduation_date')
            ->whereDate('graduation_date', $date->toDateString())
            ->get();

        $count = 0;
        foreach ($members as $m) {
            $url = route('members.show', $m);
            $years = $m->years_in_jkt48 ? " (bersama JKT48 selama {$m->years_in_jkt48} tahun)" : '';
            $telegramMsg = "🎓 <b>{$m->name}</b> lulus dari JKT48 hari ini{$years}.\n\n<a href=\"{$url}\">Profil member</a>";
            $discordMsg = "🎓 **{$m->name}** lulus dari JKT48 hari ini{$years}.\n{$url}";
            $count += $this->send('graduation', $m->id, $date, $telegramMsg, $discordMsg, $m);
        }

        return $count;
    }

    private function send(string $type, int $memberId, Carbon $date, string $telegramMsg, string $discordMsg, Member $member): int
    {
        $count = 0;
        if ($this->telegram->enabled() && ! $this->wasSent($type, $memberId, $date, 'telegram')) {
            if ($this->telegram->broadcast($telegramMsg) > 0) {
                $this->log($type, $memberId, $date, 'telegram');
                $count++;
            }
        }
        if ($this->discord->enabled() && ! $this->wasSent($type, $memberId, $date, 'discord')) {
            $embed = $this->buildDiscordEmbed($type, $member, $date);
            if ($this->discord->broadcast($discordMsg, $embed ? [$embed] : []) > 0) {
                $this->log($type, $memberId, $date, 'discord');
                $count++;
            }
        }

        return $count;
    }

    private function buildDiscordEmbed(string $type, Member $member, Carbon $date): ?array
    {
        $title = $type === 'birthday'
            ? '🎂 Ulang Tahun Member'
            : '🎓 Kelulusan Member';
        $color = $type === 'birthday' ? 0xF472B6 : 0xFACC15;
        $embed = [
            'title' => $title,
            'description' => $member->name.($member->nickname ? " ({$member->nickname})" : ''),
            'url' => route('members.show', $member),
            'color' => $color,
            'timestamp' => $date->toIso8601String(),
        ];
        if ($member->photo_url) {
            $embed['thumbnail'] = ['url' => $member->photo_url];
        }

        return $embed;
    }

    private function wasSent(string $type, int $memberId, Carbon $date, string $channel): bool
    {
        return DB::table('notification_logs')
            ->where('event_type', $type)
            ->where('member_id', $memberId)
            ->where('event_date', $date->toDateString())
            ->where('channel', $channel)
            ->exists();
    }

    private function log(string $type, int $memberId, Carbon $date, string $channel): void
    {
        DB::table('notification_logs')->insert([
            'event_type' => $type,
            'member_id' => $memberId,
            'event_date' => $date->toDateString(),
            'channel' => $channel,
            'sent_at' => now(),
        ]);
    }
}
