<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Single;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSingleSeeder extends Seeder
{
    public function run(): void
    {
        // Normalisasi nickname dari spreadsheet ke nickname resmi di DB.
        $aliasMap = [
            'Aki' => 'Akicha',
            'Shania' => 'Shanju',
            'Yuvi' => 'Yupi',
            'Thalia' => 'Tata',
            'Chikano' => 'Chikarina',
            'Azizi' => 'Zee',
            'Pucchi' => 'Puti',
            'Meme' => 'Melati',
            'Mira' => 'Amira',
        ];

        // Untuk nickname yang tidak unik di DB, resolve via nama lengkap.
        $nameOverrides = [
            'CinHap' => 'Cindy Hapsari Maharani Pujiantoro Putri', // Gen 4
            'Indah' => 'Indah Cahya',                              // Gen 9
        ];

        // Data senbatsu tiap single. Center di listing pertama, senbatsu di listing kedua
        // dalam urutan asli dari spreadsheet (dipetakan ke kolom `position`, 1-based).
        // Nickname disini masih memakai ejaan spreadsheet — dinormalisasi saat lookup.
        $singles = [
            'S1' => [
                'centers' => ['Melody', 'Kinal'],
                'senbatsu' => ['Aki', 'Ayana', 'Beby', 'Delima', 'Kinal', 'Gaby', 'Haruka', 'Jeje', 'Veranda', 'Melody', 'Nabilah', 'Rena', 'Dhike', 'Rica', 'Shania', 'Stella'],
            ],
            'S2' => [
                'centers' => ['Melody', 'Veranda'],
                'senbatsu' => ['Haruka', 'Veranda', 'Melody', 'Nabilah', 'Shania', 'Yuvi', 'Natalia', 'Viny', 'Rona', 'Naomi'],
            ],
            'S3' => [
                'centers' => ['Haruka'],
                'senbatsu' => ['Melody', 'Shania', 'Nabilah', 'Haruka', 'Veranda', 'Aki', 'Rena', 'Ayana', 'Kinal', 'Beby', 'Sonia', 'Yuvi', 'Ikha', 'Naomi', 'Rona', 'Della'],
            ],
            'S4' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Haruka', 'Melody', 'Shania', 'Nabilah', 'Veranda', 'Aki', 'Ayana', 'Kinal', 'Beby', 'Dhike', 'Yuvi', 'Naomi', 'Rona', 'Viny', 'Noella', 'Hanna'],
            ],
            'S5' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Ayana', 'Kinal', 'Ghaida', 'Jeje', 'Veranda', 'Melody', 'Nabilah', 'Haruka', 'Dhike', 'Shania', 'Sendy'],
            ],
            'S6' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Ayana', 'Beby', 'Kinal', 'Ghaida', 'Jeje', 'Veranda', 'Melody', 'Nabilah', 'Haruka', 'Rica', 'Shania', 'Yuvi', 'Hanna', 'Viny', 'Thalia', 'Yona'],
            ],
            'S7' => [
                'centers' => ['Shania'],
                'senbatsu' => ['Beby', 'Kinal', 'Haruka', 'Jeje', 'Veranda', 'Melody', 'Nabilah', 'Dhike', 'Shania', 'Vanka', 'Yuvi', 'Viny', 'Ikha', 'Chikano', 'Naomi', 'Thalia'],
            ],
            'S8' => [
                'centers' => ['Melody', 'Veranda'],
                'senbatsu' => ['Haruka', 'Ayana', 'Beby', 'Kinal', 'Ghaida', 'Veranda', 'Melody', 'Nabilah', 'Dhike', 'Shania', 'Chikano', 'Yuvi', 'Viny', 'Naomi', 'Thalia', 'Yona'],
            ],
            'S9' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Haruka', 'Beby', 'Kinal', 'Veranda', 'Melody', 'Nabilah', 'Dhike', 'Shania', 'Yuvi', 'Hanna', 'Viny', 'Rona', 'Naomi', 'Thalia', 'Andela', 'Michelle'],
            ],
            'S10' => [
                'centers' => ['Veranda'],
                'senbatsu' => ['Ayana', 'Haruka', 'Beby', 'Kinal', 'Veranda', 'Melody', 'Nabilah', 'Ghaida', 'Gaby', 'Shania', 'Sendy', 'Yuvi', 'Viny', 'Ikha', 'Sisil', 'Andela'],
            ],
            'S11' => [
                'centers' => ['Veranda'],
                'senbatsu' => ['Ayana', 'Beby', 'Elaine', 'Ghaida', 'Veranda', 'Melody', 'Nabilah', 'Shania', 'Yuvi', 'Kinal', 'Lidya', 'Sisil', 'Viny', 'Yona', 'Haruka', 'Michelle'],
            ],
            'S12' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Ayana', 'Elaine', 'Jeje', 'Veranda', 'Melody', 'Nabilah', 'Shania', 'Yuvi', 'Kinal', 'Hanna', 'Lidya', 'Viny', 'Yona', 'Haruka', 'Michelle', 'Shani'],
            ],
            'S13' => [
                'centers' => ['Veranda'],
                'senbatsu' => ['Dena', 'Gaby', 'Ghaida', 'Veranda', 'Melody', 'Nabilah', 'Shania', 'Vanka', 'Chikano', 'Yuvi', 'Kinal', 'Uty', 'Sisil', 'Michelle', 'Haruka', 'Gracia'],
            ],
            'S14' => [
                'centers' => ['Sinka'],
                'senbatsu' => ['Gaby', 'Rachel', 'Jeje', 'Sendy', 'Vanka', 'Acha', 'Kinal', 'Ikha', 'Sinka', 'Yona', 'Feni', 'Nadse', 'Haruka', 'Aurel', 'Cesen', 'Lisa'],
            ],
            'S15' => [
                'centers' => ['Nabilah'],
                'senbatsu' => ['Kinal', 'Veranda', 'Michelle', 'Nabilah', 'Haruka', 'Shania', 'Ayana', 'Beby', 'Chikano', 'Yuvi', 'Viny', 'Shani', 'Zara', 'Devi', 'Melody', 'Aya'],
            ],
            'S16' => [
                'centers' => ['Veranda'],
                'senbatsu' => ['Kinal', 'Veranda', 'Michelle', 'Nabilah', 'Shania', 'Vanka', 'Ayana', 'Beby', 'Chikano', 'Yuvi', 'Viny', 'Shani', 'Zara', 'Devi', 'Melody', 'Aya'],
            ],
            'S17' => [
                'centers' => ['Shani'],
                'senbatsu' => ['Kinal', 'Michelle', 'Nabilah', 'Saktia', 'Shania', 'Anin', 'Ayana', 'Beby', 'Yuvi', 'Frieska', 'Chikano', 'Rona', 'Shani', 'Gracia', 'Devi', 'Melody'],
            ],
            'S18' => [
                'centers' => ['Melody'],
                'senbatsu' => ['Kinal', 'Gaby', 'Michelle', 'Shania', 'Vanka', 'Yona', 'Ayana', 'Beby', 'Yuvi', 'Frieska', 'Sonia', 'Shani', 'Gracia', 'Zara', 'Devi', 'Melody'],
            ],
            'S19_EK' => [
                'centers' => ['Zara'],
                'senbatsu' => ['CinHap', 'Yuvi', 'Feni', 'Michelle', 'Aya', 'Shania', 'Anin', 'Shani', 'Gracia', 'Yona', 'Zara', 'Ayana', 'Eve', 'Devi', 'Pucchi', 'Vanka'],
            ],
            'S19_U' => [
                'centers' => ['Ayana', 'Beby'],
                'senbatsu' => ['Yuvi', 'Feni', 'Gaby', 'Shania', 'Stefi', 'Acha', 'Beby', 'Natalia', 'Aurel', 'Viny', 'Rona', 'Zara', 'Ayana', 'Jinan', 'Devi', 'Meme'],
            ],
            'S20' => [
                'centers' => ['Yuvi'],
                'senbatsu' => ['Yuvi', 'Feni', 'Michelle', 'Aya', 'Sinka', 'Stefi', 'Anin', 'Beby', 'Viny', 'Shani', 'Gracia', 'Yona', 'Ayana', 'Okta', 'Jinan', 'Aby'],
            ],
            'S21' => [
                'centers' => ['Shani'],
                'senbatsu' => ['CinHap', 'Diani', 'Feni', 'Nadila', 'Amel', 'Christy', 'Beby', 'Eli', 'Aya', 'Shani', 'Gracia', 'Aby', 'Jinan', 'Meme', 'Celine', 'Vivi'],
            ],
            'S22' => [
                'centers' => ['Shani'],
                'senbatsu' => ['Ashel', 'Mira', 'Christy', 'Anin', 'Ariel', 'Zee', 'CinHap', 'Oniel', 'Dey', 'Eve', 'Olla', 'Feni', 'Fiony', 'Flora', 'Sisca', 'Freya', 'Gaby', 'Gita', 'Eli', 'Indah', 'Jessi', 'Jesslyn', 'Jinan', 'Kathrina', 'Lulu', 'Marsha', 'Muthe', 'Adel', 'Shani', 'Gracia', 'Celine', 'Chika', 'Ara'],
            ],
            'S23' => [
                'centers' => ['Zee'],
                'senbatsu' => ['Zee', 'Shani', 'Feni', 'Gracia', 'Jinan', 'Christy', 'Chika', 'Fiony', 'Marsha'],
            ],
            'S24' => [
                'centers' => ['Shani'],
                'senbatsu' => ['Shani', 'Feni', 'Gracia', 'Gita', 'Muthe', 'Eli', 'Azizi', 'Christy', 'Fiony', 'Marsha', 'Indah', 'Kathrina'],
            ],
            'S25' => [
                'centers' => ['Marsha', 'Zee'],
                'senbatsu' => ['Zee', 'Christy', 'Gracia', 'Adel', 'Feni', 'Kathrina', 'Marsha', 'Freya', 'Fiony', 'Jessi', 'Muthe', 'Gita'],
            ],
            'S26' => [
                'centers' => ['Feni'],
                'senbatsu' => ['Feni', 'Christy', 'Jessi', 'Fiony', 'Muthe', 'Freya', 'Gita', 'Gracia', 'Marsha', 'Lia', 'Lulu', 'Oniel'],
            ],
            'S27' => [
                'centers' => ['Fiony'],
                'senbatsu' => ['Fiony', 'Marsha', 'Freya', 'Christy', 'Michie', 'Muthe', 'Jessi', 'Gita', 'Greesel', 'Kathrina', 'Olla', 'Gracie'],
            ],
        ];

        // Build lookup: nickname resmi -> member_id (untuk nickname unik saja).
        $memberIdByNickname = [];
        $duplicates = [];
        foreach (Member::whereNotNull('nickname')->where('nickname', '!=', '')->get(['id', 'nickname']) as $m) {
            if (isset($memberIdByNickname[$m->nickname])) {
                $duplicates[$m->nickname] = true;
                continue;
            }
            $memberIdByNickname[$m->nickname] = $m->id;
        }
        foreach (array_keys($duplicates) as $dupNick) {
            unset($memberIdByNickname[$dupNick]);
        }

        $memberIdByName = Member::pluck('id', 'name')->toArray();
        $singleIdByCode = Single::pluck('id', 'code')->toArray();

        $resolve = function (string $alias) use ($aliasMap, $nameOverrides, $memberIdByNickname, $memberIdByName): ?int {
            if (isset($nameOverrides[$alias])) {
                return $memberIdByName[$nameOverrides[$alias]] ?? null;
            }
            $nick = $aliasMap[$alias] ?? $alias;

            return $memberIdByNickname[$nick] ?? null;
        };

        $totalRows = 0;
        $missing = [];

        DB::transaction(function () use ($singles, $singleIdByCode, $resolve, &$totalRows, &$missing) {
            foreach ($singles as $code => $data) {
                if (! isset($singleIdByCode[$code])) {
                    $missing[] = "Single code {$code} not found";
                    continue;
                }
                $singleId = $singleIdByCode[$code];

                // Bersihkan pivot lama supaya idempotent.
                DB::table('member_singles')->where('single_id', $singleId)->delete();

                $centerIds = [];
                foreach ($data['centers'] as $alias) {
                    $id = $resolve($alias);
                    if ($id === null) {
                        $missing[] = "[{$code}] center '{$alias}' tidak ditemukan";
                        continue;
                    }
                    $centerIds[$id] = true;
                }

                $now = now();
                $rows = [];
                $position = 1;
                $seen = [];
                foreach ($data['senbatsu'] as $alias) {
                    $id = $resolve($alias);
                    if ($id === null) {
                        $missing[] = "[{$code}] senbatsu '{$alias}' tidak ditemukan";
                        continue;
                    }
                    if (isset($seen[$id])) {
                        continue; // hindari duplikat member dalam single yang sama
                    }
                    $seen[$id] = true;

                    $rows[] = [
                        'member_id' => $id,
                        'single_id' => $singleId,
                        'role' => isset($centerIds[$id]) ? 'center' : 'member',
                        'position' => $position++,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Pastikan center yang tidak muncul di list senbatsu tetap terdaftar.
                foreach (array_keys($centerIds) as $centerId) {
                    if (! isset($seen[$centerId])) {
                        $rows[] = [
                            'member_id' => $centerId,
                            'single_id' => $singleId,
                            'role' => 'center',
                            'position' => $position++,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (! empty($rows)) {
                    DB::table('member_singles')->insert($rows);
                    $totalRows += count($rows);
                }
            }
        });

        foreach ($missing as $msg) {
            $this->command->warn($msg);
        }
        $this->command->info("Seeded {$totalRows} member_singles rows across ".count($singles).' singles.');
    }
}
