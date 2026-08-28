<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MatchMemberPhotos extends Command
{
    protected $signature = 'members:match-photos {--dir=img : Public sub-directory containing the photos}
                                                  {--url-prefix= : URL prefix written to photo_url (default: /{dir}). Use e.g. /public/img when the app is served from a /public sub-path.}
                                                  {--dry-run : Only report; do not write to DB}
                                                  {--overwrite : Replace existing photo_url values}';

    protected $description = 'Match files in public/{dir} to members by name and set photo_url.';

    private array $overrides = [
        'CELLINE_THEFANI.jpg' => 'Celline Thefannie',
        'Gen1_aki_takajo.jpg' => 'Aki Takajo',
        'Gen1_gabriella.jpg' => 'Gabriela Margareth Warouw',
        'Gen1_haruka_nakagawa.jpg' => 'Haruka Nakagawa',
        'Gen2_rina_chikano.jpg' => 'Rina Chikano',
        'Gen6_Saya_Kawamoto.webp' => 'Saya Kawamoto',
        'Gen9_iris_vevina_prasetio.webp' => 'Iris Vevina Prasetio',
        'JKT48VGen1_Kanaia_Asa.webp' => 'Kanaia Asa',
        'JKT48VGen1_Tana_Nona.jpg' => 'Tana Nona',
        'JKT48VGen2_Sami_Maono.png' => 'Sami Maono',
        'AURELLIA.jpg' => 'Aurellia',
        'DESY_NATALIA.jpg' => 'Desy Natalia Ang',
        'Gen2_thalia.webp' => 'Thalia',
    ];

    public function handle(): int
    {
        $dir = trim($this->option('dir'), '/');
        $absDir = public_path($dir);

        if (! is_dir($absDir)) {
            $this->error("Directory not found: {$absDir}");

            return self::FAILURE;
        }

        $files = $this->scanFiles($absDir);
        $members = Member::with('generation:id,code')->get(['id', 'name', 'nickname', 'photo_url', 'generation_id']);

        $result = $this->processFiles($files, $members, $dir);

        $this->reportResults($files->count(), $dir, $members, $result);

        return self::SUCCESS;
    }

    private function scanFiles(string $absDir): Collection
    {
        return collect(File::files($absDir))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']));
    }

    private function processFiles(Collection $files, Collection $members, string $dir): array
    {
        $matches = [];
        $orphans = [];
        $ambiguous = [];
        $updated = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $picked = $this->pickMember($filename, $members, $orphans, $ambiguous);
            if (! $picked) {
                continue;
            }

            $matches[$filename] = $picked;
            $status = $this->applyPhotoUrl($picked, $filename, $dir);
            if ($status === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }
        }

        return compact('matches', 'orphans', 'ambiguous', 'updated', 'skipped');
    }

    private function pickMember(string $filename, Collection $members, array &$orphans, array &$ambiguous): ?Member
    {
        if (isset($this->overrides[$filename])) {
            $picked = $members->firstWhere('name', $this->overrides[$filename]);
            if (! $picked) {
                $orphans[] = "{$filename}  (override target '{$this->overrides[$filename]}' not in DB)";

                return null;
            }

            return $picked;
        }

        $tokens = $this->fileTokens($filename);
        if (! $tokens) {
            $orphans[] = $filename;

            return null;
        }

        $scored = $this->scoreCandidates($members, $tokens, $this->extractGenHint($filename));
        if ($scored->isEmpty()) {
            $orphans[] = $filename;

            return null;
        }

        $top = $scored->first();
        $tie = $scored->where('score', $top['score']);
        if ($tie->count() > 1) {
            $ambiguous[$filename] = $tie->pluck('member.name')->all();

            return null;
        }

        return $top['member'];
    }

    private function scoreCandidates(Collection $members, array $tokens, ?string $genHint): Collection
    {
        return $members->map(function (Member $m) use ($tokens, $genHint) {
            $nameNorm = $this->normalize($m->name.' '.($m->nickname ?: ''));
            $nameWords = preg_split('/\s+/', $nameNorm);

            foreach ($tokens as $t) {
                if (! Str::contains($nameNorm, $t)) {
                    return null;
                }
            }

            $score = -max(0, count($nameWords) - count($tokens));
            if ($genHint !== null && $m->generation && stripos($m->generation->code, $genHint) !== false) {
                $score += 5;
            }

            return ['member' => $m, 'score' => $score];
        })->filter()->sortByDesc('score')->values();
    }

    private function applyPhotoUrl(Member $picked, string $filename, string $dir): string
    {
        $prefix = rtrim($this->option('url-prefix') ?: ('/'.$dir), '/');
        $newUrl = $prefix.'/'.$filename;
        $current = $picked->photo_url;

        if ($current === $newUrl) {
            return 'skipped';
        }
        if ($current && ! $this->option('overwrite')) {
            return 'skipped';
        }

        if (! $this->option('dry-run')) {
            $picked->photo_url = $newUrl;
            $picked->save();
        }

        return 'updated';
    }

    private function reportResults(int $scanned, string $dir, Collection $members, array $result): void
    {
        $this->reportSummary($scanned, $dir, $result);
        $this->reportOrphans($result['orphans']);
        $this->reportAmbiguous($result['ambiguous']);
        $this->reportMissing($members, $result['matches']);
    }

    private function reportSummary(int $scanned, string $dir, array $result): void
    {
        $suffix = $this->option('dry-run') ? ' (dry-run, not persisted)' : '';
        $this->newLine();
        $this->info("Scanned: {$scanned} files in public/{$dir}");
        $this->info('Matched: '.count($result['matches']));
        $this->info("Updated: {$result['updated']}{$suffix}");
        $this->info("Skipped (already set): {$result['skipped']}");
    }

    private function reportOrphans(array $orphans): void
    {
        $this->warn('Orphan files (no member match): '.count($orphans));
        foreach ($orphans as $o) {
            $this->line("   - {$o}");
        }
    }

    private function reportAmbiguous(array $ambiguous): void
    {
        if (! $ambiguous) {
            return;
        }
        $this->warn('Ambiguous files (matched multiple members with equal score):');
        foreach ($ambiguous as $f => $names) {
            $this->line("   - {$f}  ->  ".implode(' | ', $names));
        }
    }

    private function reportMissing(Collection $members, array $matches): void
    {
        $matchedIds = collect($matches)->pluck('id')->all();
        $without = $members->filter(fn (Member $m) => ! in_array($m->id, $matchedIds, true) && empty($m->photo_url));

        $this->warn('Members WITHOUT any photo: '.$without->count());
        foreach ($without->sortBy(fn ($m) => ($m->generation->code ?? 'zzz').'_'.$m->name) as $m) {
            $gen = $m->generation->code ?? '-';
            $nickname = $m->nickname ? " ({$m->nickname})" : '';
            $this->line("   - [Gen {$gen}] {$m->name}{$nickname}");
        }
    }

    private function fileTokens(string $filename): array
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $base = preg_replace('/^(?:JKT48)?V?Gen[A-Za-z0-9]+_/i', '', $base);
        $base = strtolower($base);
        $base = str_replace(['-', '.'], '_', $base);
        $tokens = array_filter(explode('_', $base), fn ($t) => $t !== '' && strlen($t) >= 2);

        return array_values($tokens);
    }

    private function extractGenHint(string $filename): ?string
    {
        if (preg_match('/^(?:JKT48)?V?Gen([A-Za-z0-9]+)_/i', $filename, $m)) {
            $code = $m[1];
            if (ctype_digit($code)) {
                $code = (string) intval($code);
            }

            return $code;
        }

        return null;
    }

    private function normalize(string $normalized): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($normalized)));
    }
}
