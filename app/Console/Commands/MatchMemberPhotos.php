<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MatchMemberPhotos extends Command
{
    protected $signature = 'members:match-photos {--dir=img : Public sub-directory containing the photos}
                                                  {--url-prefix= : URL prefix written to photo_url (default: /{dir}). Use e.g. /public/img when the app is served from a /public sub-path.}
                                                  {--dry-run : Only report; do not write to DB}
                                                  {--overwrite : Replace existing photo_url values}';

    protected $description = 'Match files in public/{dir} to members by name and set photo_url.';

    /**
     * Explicit filename -> exact member name overrides.
     * Use these when tokenized matching cannot bridge a spelling gap
     * (Celline Thefani vs Thefannie, Gabriella vs Gabriela …) or when
     * two members share every token from a filename.
     */
    private array $overrides = [
        // Spelling / prefix / generation-code mismatches
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

        // Ambiguous — force preferred candidate
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

        $files = collect(File::files($absDir))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif']));

        $members = Member::with('generation:id,code')->get(['id', 'name', 'nickname', 'photo_url', 'generation_id']);

        $matches = [];
        $orphans = [];
        $ambiguous = [];
        $updated = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();

            // ---- 1. explicit override ----
            $picked = null;
            if (isset($this->overrides[$filename])) {
                $picked = $members->firstWhere('name', $this->overrides[$filename]);
                if (! $picked) {
                    $orphans[] = "{$filename}  (override target '{$this->overrides[$filename]}' not in DB)";

                    continue;
                }
            }

            // ---- 2. tokenized name match ----
            if (! $picked) {
                $tokens = $this->fileTokens($filename);
                if (! $tokens) {
                    $orphans[] = $filename;

                    continue;
                }
                $genHint = $this->extractGenHint($filename);

                $scored = $members->map(function (Member $m) use ($tokens, $genHint) {
                    $nameNorm = $this->normalize($m->name.' '.($m->nickname ?: ''));
                    $nameWords = preg_split('/\s+/', $nameNorm);

                    foreach ($tokens as $t) {
                        if (! Str::contains($nameNorm, $t)) {
                            return null;
                        }
                    }

                    // Score: fewer extra name words = better; gen hint match = bonus
                    $extraWords = max(0, count($nameWords) - count($tokens));
                    $score = -$extraWords;
                    if ($genHint !== null && $m->generation && stripos($m->generation->code, $genHint) !== false) {
                        $score += 5;
                    }

                    return ['member' => $m, 'score' => $score];
                })->filter()->sortByDesc('score')->values();

                if ($scored->isEmpty()) {
                    $orphans[] = $filename;

                    continue;
                }

                $top = $scored->first();
                $tie = $scored->where('score', $top['score']);
                if ($tie->count() > 1) {
                    $ambiguous[$filename] = $tie->pluck('member.name')->all();

                    continue;
                }
                $picked = $top['member'];
            }

            /** @var Member $picked */
            $matches[$filename] = $picked;
            $prefix = rtrim($this->option('url-prefix') ?: ('/'.$dir), '/');
            $newUrl = $prefix.'/'.$filename;
            $current = $picked->photo_url;

            if ($current === $newUrl) {
                $skipped++;

                continue;
            }
            if ($current && ! $this->option('overwrite')) {
                $skipped++;

                continue;
            }

            if (! $this->option('dry-run')) {
                $picked->photo_url = $newUrl;
                $picked->save();
            }
            $updated++;
        }

        // Members without any photo (neither matched nor pre-existing)
        $matchedIds = collect($matches)->pluck('id')->all();
        $membersWithoutPhoto = $members->filter(function (Member $m) use ($matchedIds) {
            return ! in_array($m->id, $matchedIds, true) && empty($m->photo_url);
        });

        // ---------- Report ----------
        $this->newLine();
        $this->info('Scanned: '.$files->count()." files in public/{$dir}");
        $this->info('Matched: '.count($matches));
        $this->info("Updated: {$updated}".($this->option('dry-run') ? ' (dry-run, not persisted)' : ''));
        $this->info("Skipped (already set): {$skipped}");
        $this->warn('Orphan files (no member match): '.count($orphans));
        foreach ($orphans as $o) {
            $this->line("   - {$o}");
        }
        if ($ambiguous) {
            $this->warn('Ambiguous files (matched multiple members with equal score):');
            foreach ($ambiguous as $f => $names) {
                $this->line("   - {$f}  ->  ".implode(' | ', $names));
            }
        }
        $this->warn('Members WITHOUT any photo: '.$membersWithoutPhoto->count());
        foreach ($membersWithoutPhoto->sortBy(fn ($m) => ($m->generation->code ?? 'zzz').'_'.$m->name) as $m) {
            $gen = $m->generation->code ?? '-';
            $this->line("   - [Gen {$gen}] {$m->name}".($m->nickname ? " ({$m->nickname})" : ''));
        }

        return self::SUCCESS;
    }

    private function fileTokens(string $filename): array
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        // Strip common generation prefixes: Gen1_, Gen10_, GenV1_, JKT48VGen1_, JKT48VGen2_, etc.
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

    private function normalize(string $s): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($s)));
    }
}
