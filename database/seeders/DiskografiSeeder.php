<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\CouplingSong;
use App\Models\Member;
use App\Models\MvLocation;
use App\Models\Setlist;
use App\Models\Single;
use App\Models\Song;
use App\Models\SubUnit;
use App\Models\SubUnitSong;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiskografiSeeder extends Seeder
{
    private array $missing = [];

    private array $memberIdByNickname = [];

    private array $memberIdByName = [];

    public function run(): void
    {
        $this->buildMemberIndex();
        $singleIdByCode = $this->syncSingles();
        $songIdByExternal = $this->syncSongs($singleIdByCode);
        $this->syncAlbums($songIdByExternal);
        $this->syncSubUnits();
        $this->syncCouplingSongs($singleIdByCode);
        $this->syncSetlists($songIdByExternal);
        $this->syncMvLocations();

        foreach ($this->missing as $msg) {
            $this->command->warn($msg);
        }
    }

    private function dataPath(string $file): string
    {
        return database_path('data/diskografi/'.$file);
    }

    private function loadJson(string $file): array
    {
        $path = $this->dataPath($file);
        if (! is_file($path)) {
            $this->command->warn("File tidak ditemukan: {$path}");

            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function buildMemberIndex(): void
    {
        $duplicates = [];
        foreach (Member::whereNotNull('nickname')->where('nickname', '!=', '')->get(['id', 'nickname']) as $m) {
            if (isset($this->memberIdByNickname[$m->nickname])) {
                $duplicates[$m->nickname] = true;

                continue;
            }
            $this->memberIdByNickname[$m->nickname] = $m->id;
        }
        foreach (array_keys($duplicates) as $dup) {
            unset($this->memberIdByNickname[$dup]);
        }

        $this->memberIdByName = Member::pluck('id', 'name')->toArray();
    }

    /** Reuse mapping dari MemberSingleSeeder. */
    private function resolveMember(string $alias): ?int
    {
        static $aliasMap = [
            'Aki' => 'Akicha',
            'Shania' => 'Shanju',
            'Yuvi' => 'Yupi',
            'Thalia' => 'Tata',
            'Chikano' => 'Chikarina',
            'Azizi' => 'Zee',
            'Pucchi' => 'Puti',
            'Meme' => 'Melati',
            'Mira' => 'Amira',
            'Vienny' => 'Viny', // Ratu Vienny Fitrilya (G2)
        ];

        static $nameOverrides = [
            'CinHap' => 'Cindy Hapsari Maharani Pujiantoro Putri',
            'Indah' => 'Indah Cahya',
            // Nickname ambigu — dipetakan via nama lengkap sesuai konteks coupling.
            'Cindy' => 'Cindy Hapsari Maharani Pujiantoro Putri',
            'Ella' => 'Gabriela Abigail Mewengkang',
        ];

        if (isset($nameOverrides[$alias])) {
            return $this->memberIdByName[$nameOverrides[$alias]] ?? null;
        }

        $nick = $aliasMap[$alias] ?? $alias;

        return $this->memberIdByNickname[$nick] ?? null;
    }

    /**
     * Pemetaan `single_ref` di sheet Semua Lagu / Coupling → code di tabel `singles`.
     * Menggunakan judul ID yang muncul di spreadsheet Diskografi.
     */
    private function singleRefMap(): array
    {
        return [
            'RIVER' => ['S1'],
            'Apakah Kau Melihat Mentari Senja?' => ['S2'],
            'Fortune Cookie yang Mencinta' => ['S3'],
            'Musim Panas Sounds Good!' => ['S4'],
            'Flying Get' => ['S5'],
            'Gingham Check' => ['S6'],
            'Papan Penanda Isi Hati' => ['S7'],
            'Angin Sedang Berhembus' => ['S8'],
            'Pareo adalah Emerald' => ['S9'],
            'Refrain Penuh Harapan' => ['S10'],
            'Halloween Night' => ['S11'],
            'Beginner' => ['S12'],
            'Hanya Lihat ke Depan' => ['S13'],
            'LOVE TRIP' => ['S14'],
            'Luar Biasa' => ['S15'],
            'So Long!' => ['S16'],
            'Indahnya Senyum Manismu dst.' => ['S17'],
            'Dirimu Melody' => ['S18'],
            'Everyday, Kachuusha / UZA' => ['S19_EK', 'S19_U'],
            'High Tension' => ['S20'],
            'Rapsodi' => ['S21'],
            'Cara Ceroboh untuk Mencinta' => ['S22'],
            'Flying High' => ['S23'],
            'Sayonara Crawl' => ['S24'],
            'Magic Hour' => ['S25'],
            '#KuSangatSuka' => ['S26'],
            "Andai 'Ku Bukan Idola" => ['S27'],
        ];
    }

    /** ---------- Singles ---------- */
    private function syncSingles(): array
    {
        $rows = $this->loadJson('singles.json');
        $singleIdByCode = Single::pluck('id', 'code')->toArray();

        foreach ($rows as $row) {
            $code = $this->normalizeCode($row);
            if (! $code) {
                continue;
            }

            $releaseYear = $row['release_year'] ?? null;

            $attrs = [
                'title_jp' => $row['title_jp'],
                'origin_group' => $row['origin_group'],
                'release_year' => $releaseYear,
                'mv_title' => $row['mv_title'] ?? null,
                'cover_art_url' => $row['cover_art_url'] ?? null,
                'audio_file' => $row['audio_file'] ?? null,
            ];

            // Backfill judul & release_date bila kosong.
            $existing = Single::where('code', $code)->first();
            if (! $existing) {
                $attrs['title'] = $row['title'];
                $attrs['sequence'] = (int) $row['sequence_raw'];
                $existing = Single::create(array_merge(['code' => $code], $attrs));
            } else {
                $existing->fill($attrs);
                if (empty($existing->release_date) && $releaseYear) {
                    // Placeholder: 1 Januari tahun rilis bila tanggal spesifik belum ada.
                    $existing->release_date = "{$releaseYear}-01-01";
                }
                $existing->save();
            }

            $singleIdByCode[$code] = $existing->id;
        }

        $this->command->info('Enriched '.count($rows).' singles from Excel.');

        return $singleIdByCode;
    }

    private function normalizeCode(array $row): ?string
    {
        $seq = (int) $row['sequence_raw'];
        $code = $row['code'] ?? "S{$seq}";
        // Row 19 di Excel hanya satu, tapi app pakai dua kode (S19_EK, S19_U);
        // spreadsheet row diselaraskan ke S19_EK (baris 19), UZA (S19_U) tidak diisi ulang.
        return $code;
    }

    /** ---------- Songs ---------- */
    private function syncSongs(array $singleIdByCode): array
    {
        $rows = $this->loadJson('songs.json');
        $refMap = $this->singleRefMap();
        $songIdByExternal = [];

        DB::transaction(function () use ($rows, $refMap, $singleIdByCode, &$songIdByExternal) {
            foreach ($rows as $row) {
                $singleId = null;
                if ($ref = $row['single_ref']) {
                    // Multi single ref (e.g. "RIVER, Flying Get") → ambil yang pertama saja.
                    $primary = explode(',', $ref)[0];
                    $primary = trim($primary);
                    if (isset($refMap[$primary])) {
                        $code = $refMap[$primary][0];
                        $singleId = $singleIdByCode[$code] ?? null;
                    }
                }

                $song = Song::updateOrCreate(
                    ['external_id' => $row['external_id'], 'title' => $row['title']],
                    [
                        'title_original' => $row['title_original'],
                        'origin_group' => $row['origin_group'],
                        'single_id' => $singleId,
                        'single_ref_raw' => $row['single_ref'],
                        'other_compilations' => $row['other_compilations'],
                        'setlist' => $row['setlist'],
                        'special_setlist' => $row['special_setlist'],
                        'debut_date' => $row['debut_date'],
                        'debut_at' => $row['debut_at'],
                        'released' => $row['released'],
                        'preview_url' => $row['preview_url'],
                        'mv_title' => $row['mv_title'],
                    ]
                );

                $songIdByExternal[$row['external_id']][] = $song->id;
            }
        });

        $this->command->info('Synced '.count($rows).' songs.');

        return $songIdByExternal;
    }

    /** ---------- Albums + EPs ---------- */
    private function syncAlbums(array $songIdByExternal): void
    {
        $albums = $this->loadJson('albums.json');
        $eps = $this->loadJson('eps.json');
        $all = array_merge($albums, $eps);

        // Index song id by normalized title, agar tracklist bisa auto-link.
        $songIdByTitle = [];
        foreach (Song::pluck('id', 'title') as $title => $id) {
            $songIdByTitle[$this->normalizeTitle($title)] = $id;
        }

        DB::transaction(function () use ($all, $songIdByTitle) {
            foreach ($all as $row) {
                $code = ($row['type'] === 'ep' ? 'ep-' : 'album-').$row['sequence'];

                $album = Album::updateOrCreate(
                    ['code' => $code],
                    [
                        'type' => $row['type'],
                        'title' => $row['title'],
                        'title_jp' => $row['title_jp'],
                        'sequence' => $row['sequence'],
                        'release_date' => $row['release_date'],
                        'cover_url' => $row['cover_url'],
                    ]
                );

                $album->tracks()->delete();
                foreach ($row['tracks'] as $track) {
                    $songId = $songIdByTitle[$this->normalizeTitle($track['title'])] ?? null;
                    $album->tracks()->create([
                        'position' => $track['position'],
                        'title' => $track['title'],
                        'song_id' => $songId,
                    ]);
                }
            }
        });

        $this->command->info('Synced '.count($all).' albums/EPs.');
    }

    private function normalizeTitle(string $title): string
    {
        $t = Str::lower($title);
        // Buang bracketed variants (New Era Version), Kimi no Koto ga Suki Dakara etc.
        $t = preg_replace('/\s*\(.*?\)\s*/', ' ', $t);
        $t = preg_replace('/[^a-z0-9]+/', ' ', $t);

        return trim($t);
    }

    /** ---------- Sub Units ---------- */
    private function syncSubUnits(): void
    {
        $rows = $this->loadJson('sub_unit_songs.json');
        $count = 0;

        DB::transaction(function () use ($rows, &$count) {
            $cache = [];
            foreach ($rows as $row) {
                $name = $row['sub_unit'];
                if (! isset($cache[$name])) {
                    $unit = SubUnit::updateOrCreate(['name' => $name]);
                    $cache[$name] = $unit->id;
                }
                $subUnitId = $cache[$name];

                // Idempotent per (sub_unit_id, title, debut_date).
                SubUnitSong::updateOrCreate(
                    [
                        'sub_unit_id' => $subUnitId,
                        'title' => $row['title'],
                        'debut_date' => $row['debut_date'],
                    ],
                    [
                        'title_original' => $row['title_original'],
                        'origin_group' => $row['origin_group'],
                        'debut_at' => $row['debut_at'],
                        'released' => $row['released'],
                        'has_mv' => $row['has_mv'],
                        'preview_url' => $row['preview_url'],
                    ]
                );
                $count++;
            }
        });

        $this->command->info("Synced {$count} sub-unit songs.");
    }

    /** ---------- Coupling songs ---------- */
    private function syncCouplingSongs(array $singleIdByCode): void
    {
        $rows = $this->loadJson('coupling_songs.json');
        $refMap = $this->singleRefMap();

        DB::transaction(function () use ($rows, $singleIdByCode) {
            // Bersihkan tabel supaya idempotent — pakai delete() (bukan truncate)
            // agar aman terhadap FK constraint di MySQL.
            DB::table('coupling_song_members')->delete();
            DB::table('coupling_songs')->delete();

            foreach ($rows as $row) {
                $singleId = $singleIdByCode[$row['single_code']] ?? null;
                if (! $singleId) {
                    $this->missing[] = "Coupling: single {$row['single_code']} tidak ditemukan";

                    continue;
                }

                $coupling = CouplingSong::create([
                    'single_id' => $singleId,
                    'title' => $row['title'],
                    'title_jp' => $row['title_jp'],
                    'origin_group' => $row['origin_group'],
                    'release_year' => $row['release_year'],
                    'mv_title' => $row['mv_title'],
                    'audio_file' => $row['audio_file'],
                ]);

                $centerIds = [];
                foreach ($row['centers'] as $alias) {
                    $id = $this->resolveMember($alias);
                    if ($id === null) {
                        $this->missing[] = "[coupling {$row['title']}] center '{$alias}' tidak ditemukan";

                        continue;
                    }
                    $centerIds[$id] = true;
                }

                $position = 1;
                $seen = [];
                foreach ($row['members'] as $alias) {
                    $id = $this->resolveMember($alias);
                    if ($id === null) {
                        $this->missing[] = "[coupling {$row['title']}] member '{$alias}' tidak ditemukan";

                        continue;
                    }
                    if (isset($seen[$id])) {
                        continue;
                    }
                    $seen[$id] = true;

                    $coupling->members()->attach($id, [
                        'role' => isset($centerIds[$id]) ? 'center' : 'member',
                        'position' => $position++,
                    ]);
                }

                foreach (array_keys($centerIds) as $cid) {
                    if (! isset($seen[$cid])) {
                        $coupling->members()->attach($cid, [
                            'role' => 'center',
                            'position' => $position++,
                        ]);
                    }
                }
            }
        });

        $this->command->info('Synced '.count($rows).' coupling songs.');
    }

    /** ---------- Setlists ---------- */
    private function syncSetlists(array $songIdByExternal): void
    {
        $data = $this->loadJson('setlists.json');
        $total = 0;

        DB::transaction(function () use ($data, $songIdByExternal, &$total) {
            foreach (['regular', 'special'] as $type) {
                foreach ($data[$type] ?? [] as $entry) {
                    $setlist = Setlist::updateOrCreate(
                        ['name' => $entry['name'], 'type' => $type]
                    );

                    $syncData = [];
                    $pos = 1;
                    foreach ($entry['song_external_ids'] as $extId) {
                        foreach ($songIdByExternal[$extId] ?? [] as $songId) {
                            $syncData[$songId] = ['position' => $pos++];
                        }
                    }
                    $setlist->songs()->sync($syncData);
                    $total++;
                }
            }
        });

        $this->command->info("Synced {$total} setlists (regular + special).");
    }

    /** ---------- MV Locations ---------- */
    private function syncMvLocations(): void
    {
        $rows = $this->loadJson('mv_locations.json');

        DB::transaction(function () use ($rows) {
            DB::table('mv_locations')->delete();
            foreach ($rows as $row) {
                $locations = $row['locations'] ?: [null];
                $pos = 1;
                foreach ($locations as $loc) {
                    if (! $loc) {
                        continue;
                    }
                    MvLocation::create([
                        'category' => $row['category'],
                        'song_title' => $row['song_title'],
                        'song_title_jp' => $row['song_title_jp'],
                        'release_year' => $row['release_year'],
                        'location' => $loc,
                        'position' => $pos++,
                    ]);
                }
            }
        });

        $this->command->info('Synced '.count($rows).' MV location entries.');
    }
}
