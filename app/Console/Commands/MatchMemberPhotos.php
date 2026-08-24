<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MatchMemberPhotos extends Command
{
    protected $signature = 'members:match-photos {--dir=img : Public sub-directory containing the photos}
                                                  {--dry-run : Only report; do not write to DB}
                                                  {--overwrite : Replace existing photo_url values}';
    protected $description = 'Match files in public/{dir} to members by name and set photo_url.';

    public function handle(): int
    {
        $dir = trim($this->option('dir'), '/');
        $absDir = public_path($dir);

        if (! is_dir($absDir)) {
            $this->error("Directory not found: {$absDir}");
            return self::FAILURE;
        }

        $files = collect(File::files($absDir))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), ['jpg','jpeg','png','webp','gif']));

        $members = Member::with('generation:id,code')->get(['id', 'name', 'nickname', 'photo_url', 'generation_id']);

        $matches   = [];   // filename => Member
        $orphans   = [];   // filenames with no member match
        $ambiguous = [];   // filenames with >1 match
        $updated   = 0;
        $skipped   = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $tokens = $this->fileTokens($filename);
            $genHint = $this->extractGenHint($filename);

            $candidates = $members->filter(function (Member $m) use ($tokens, $genHint) {
                $nameNorm = $this->normalize($m->name . ' ' . ($m->nickname ?: ''));
                foreach ($tokens as $t) {
                    if (! Str::contains($nameNorm, $t)) return false;
                }
                if ($genHint !== null && $m->generation && $m->generation->code !== $genHint) {
                    return false;
                }
                return true;
            });

            if ($candidates->isEmpty()) {
                $orphans[] = $filename;
                continue;
            }
            if ($candidates->count() > 1) {
                $ambiguous[$filename] = $candidates->pluck('name')->all();
                continue;
            }

            /** @var Member $m */
            $m = $candidates->first();
            $matches[$filename] = $m;

            $newUrl = '/'.$dir.'/'.$filename;
            $current = $m->photo_url;

            if ($current === $newUrl) { $skipped++; continue; }
            if ($current && ! $this->option('overwrite')) { $skipped++; continue; }

            if (! $this->option('dry-run')) {
                $m->photo_url = $newUrl;
                $m->save();
            }
            $updated++;
        }

        // Members without a matched file
        $matchedIds = collect($matches)->pluck('id')->all();
        $membersWithoutPhoto = $members->filter(function (Member $m) use ($matchedIds) {
            $hasFileMatch = in_array($m->id, $matchedIds, true);
            $hasStoredPhoto = ! empty($m->photo_url);
            return ! $hasFileMatch && ! $hasStoredPhoto;
        });

        // ---------- Report ----------
        $this->newLine();
        $this->info("Scanned: " . $files->count() . " files in public/{$dir}");
        $this->info("Matched: " . count($matches));
        $this->info("Updated: {$updated}" . ($this->option('dry-run') ? ' (dry-run, not persisted)' : ''));
        $this->info("Skipped (already set): {$skipped}");
        $this->warn("Orphan files (no member match): " . count($orphans));
        foreach ($orphans as $o) $this->line("   - {$o}");
        if ($ambiguous) {
            $this->warn("Ambiguous files (matched multiple members):");
            foreach ($ambiguous as $f => $names) {
                $this->line("   - {$f}  ->  " . implode(' | ', $names));
            }
        }
        $this->warn("Members WITHOUT any photo: " . $membersWithoutPhoto->count());
        foreach ($membersWithoutPhoto->sortBy(fn ($m) => ($m->generation->code ?? 'zzz') . '_' . $m->name) as $m) {
            $gen = $m->generation->code ?? '-';
            $this->line("   - [Gen {$gen}] {$m->name}" . ($m->nickname ? " ({$m->nickname})" : ''));
        }

        return self::SUCCESS;
    }

    /** Extract meaningful name tokens from a filename. */
    private function fileTokens(string $filename): array
    {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        // strip generation prefix like Gen1_ / Gen10_ / GenV1_
        $base = preg_replace('/^Gen[A-Za-z0-9]+_/i', '', $base);
        $base = strtolower($base);
        $base = str_replace(['-', '.'], '_', $base);
        $tokens = array_filter(explode('_', $base), fn ($t) => $t !== '' && strlen($t) >= 2);
        return array_values($tokens);
    }

    private function extractGenHint(string $filename): ?string
    {
        if (preg_match('/^Gen([A-Za-z0-9]+)_/i', $filename, $m)) {
            $code = $m[1];
            // Normalize numeric strings (e.g. "01" -> "1")
            if (ctype_digit($code)) $code = (string) intval($code);
            return $code;
        }
        return null;
    }

    private function normalize(string $s): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($s)));
    }
}
