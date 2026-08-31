<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('captains', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('member_id')
                ->constrained('teams')->nullOnDelete();
            $table->string('role', 30)->nullable()->after('team_id');
        });

        $teamMap = [
            'J' => null,
            'KIII' => null,
            'T' => null,
        ];
        foreach (['J', 'KIII', 'T'] as $code) {
            $teamMap[$code] = DB::table('teams')->where('code', $code)->value('id');
            if (! $teamMap[$code]) {
                $teamMap[$code] = DB::table('teams')->insertGetId([
                    'code' => $code,
                    'name' => 'Tim '.$code,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $rows = DB::table('captains')->get(['id', 'position']);
        foreach ($rows as $r) {
            [$role, $teamId] = $this->parsePosition($r->position, $teamMap);
            DB::table('captains')->where('id', $r->id)->update([
                'role' => $role,
                'team_id' => $teamId,
            ]);
        }

        Schema::table('captains', function (Blueprint $table) {
            $table->dropIndex(['position']);
        });
        Schema::table('captains', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }

    public function down(): void
    {
        Schema::table('captains', function (Blueprint $table) {
            $table->enum('position', [
                'Kapten JKT48',
                'Wakil Kapten JKT48',
                'Kapten Tim J',
                'Kapten Tim KIII',
                'Kapten Tim T',
            ])->nullable()->after('member_id');
        });

        $teams = DB::table('teams')->pluck('code', 'id')->all();
        $rows = DB::table('captains')->get(['id', 'role', 'team_id']);
        foreach ($rows as $r) {
            $teamCode = $r->team_id ? ($teams[$r->team_id] ?? null) : null;
            $pos = $teamCode ? "{$r->role} Tim {$teamCode}" : "{$r->role} JKT48";
            DB::table('captains')->where('id', $r->id)->update(['position' => $pos]);
        }

        Schema::table('captains', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id', 'role']);
            $table->index('position');
        });
    }

    private function parsePosition(?string $position, array $teamMap): array
    {
        if (! $position) {
            return ['Kapten', null];
        }
        if ($position === 'Kapten JKT48') {
            return ['Kapten', null];
        }
        if ($position === 'Wakil Kapten JKT48') {
            return ['Wakil Kapten', null];
        }
        foreach ($teamMap as $code => $teamId) {
            if ($position === "Kapten Tim {$code}") {
                return ['Kapten', $teamId];
            }
            if ($position === "Wakil Kapten Tim {$code}") {
                return ['Wakil Kapten', $teamId];
            }
        }
        return ['Kapten', null];
    }
};
