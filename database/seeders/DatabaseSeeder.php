<?php

namespace Database\Seeders;

use App\Models\Captain;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database from the JKT48 Excel file.
     *
     * Requires: phpoffice/phpspreadsheet
     *   composer require phpoffice/phpspreadsheet
     *
     * Place the Excel file at: database/seeders/data/JKT48_Database.xlsx
     */
    public function run(): void
    {
        $file = database_path('seeders/data/JKT48_Database.xlsx');
        if (! file_exists($file)) {
            $this->command->error("Excel file not found at {$file}");
            return;
        }

        DB::transaction(function () use ($file) {
            $spreadsheet = IOFactory::load($file);

            $this->seedGenerations($spreadsheet);
            $this->seedSingles($spreadsheet);
            $this->seedMembers($spreadsheet);
            $this->seedCaptains($spreadsheet);
        });

        $this->command->info('JKT48 database seeded successfully.');
    }

    private function seedGenerations($spreadsheet): void
    {
        // Read generation info from Rekap sheet
        $sheet = $spreadsheet->getSheetByName('Rekap');
        if (! $sheet) return;

        $generationJoinDates = [];
        for ($row = 2; $row <= 20; $row++) {
            $code = $sheet->getCell("A{$row}")->getValue();
            $date = $sheet->getCell("B{$row}")->getValue();
            if (! $code) continue;

            $code = (string) $code;
            if (is_numeric($code)) {
                $code = (string) intval($code);
            }
            $generationJoinDates[$code] = $this->excelDate($date);
        }

        // Standard generation names
        $names = [
            '1' => 'Generasi 1', '2' => 'Generasi 2', '3' => 'Generasi 3',
            '4' => 'Generasi 4', '5' => 'Generasi 5', '6' => 'Generasi 6',
            '7' => 'Generasi 7', '8' => 'Generasi 8', '9' => 'Generasi 9',
            '10' => 'Generasi 10', '11' => 'Generasi 11', '12' => 'Generasi 12',
            '13' => 'Generasi 13',
            'V1' => 'Vocal Generasi 1',
            'V2' => 'Vocal Generasi 2',
            'Kaigai 1' => 'Kaigai Generasi 1',
            'Kaigai 2' => 'Kaigai Generasi 2',
            'Transfer' => 'Transfer Member',
        ];

        foreach ($names as $code => $name) {
            Generation::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'join_date' => $generationJoinDates[$code] ?? null,
                ]
            );
        }

        $this->command->info('Seeded ' . Generation::count() . ' generations.');
    }

    private function seedSingles($spreadsheet): void
    {
        $sheet = $spreadsheet->getSheetByName('Single');
        if (! $sheet) return;

        // Row 1: header (Single, S1, S2, ...)
        // Row 2: Judul (titles)
        // Row 3: Tanggal Rilis (release dates)
        $col = 2;
        $sequence = 1;
        while (true) {
            $code = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if (! $code) break;

            $title = $sheet->getCellByColumnAndRow($col, 2)->getValue();
            $releaseDate = $sheet->getCellByColumnAndRow($col, 3)->getValue();

            if ($title) {
                Single::updateOrCreate(
                    ['code' => (string) $code],
                    [
                        'title' => (string) $title,
                        'release_date' => $this->excelDate($releaseDate),
                        'sequence' => $sequence,
                    ]
                );
                $sequence++;
            }
            $col++;
        }

        $this->command->info('Seeded ' . Single::count() . ' singles.');
    }

    private function seedMembers($spreadsheet): void
    {
        $sheet = $spreadsheet->getSheetByName('Member');
        if (! $sheet) return;

        $generationMap = Generation::pluck('id', 'code')->toArray();
        $singleMap = Single::pluck('id', 'code')->toArray();

        // Determine which columns are the single columns (S1, S2, ...)
        $singleColumns = [];
        for ($col = 26; $col <= 100; $col++) {
            $header = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if (! $header) break;
            if (isset($singleMap[$header])) {
                $singleColumns[$col] = $singleMap[$header];
            }
        }

        $count = 0;
        for ($row = 2; $row <= 300; $row++) {
            $name = $sheet->getCell("A{$row}")->getValue();
            if (! $name) break;

            $genCode = (string) $sheet->getCell("G{$row}")->getValue();
            if (is_numeric($genCode)) $genCode = (string) intval($genCode);
            $genId = $generationMap[$genCode] ?? null;
            if (! $genId) continue;

            $joinDate = $this->excelDate($sheet->getCell("H{$row}")->getValue());
            $graduationDate = $this->excelDate($sheet->getCell("T{$row}")->getValue());

            $member = Member::updateOrCreate(
                ['name' => (string) $name],
                [
                    'nickname' => $sheet->getCell("B{$row}")->getValue() ?: null,
                    'birth_place' => $sheet->getCell("C{$row}")->getValue() ?: null,
                    'birth_date' => $this->excelDate($sheet->getCell("D{$row}")->getValue()),
                    'generation_id' => $genId,
                    'join_date' => $joinDate,
                    'cancelled_date' => $this->excelDate($sheet->getCell("J{$row}")->getValue()),
                    'rejoin_date' => $this->excelDate($sheet->getCell("L{$row}")->getValue()),
                    'promotion_date' => $this->excelDate($sheet->getCell("N{$row}")->getValue()),
                    'graduation_announce_date' => $this->excelDate($sheet->getCell("P{$row}")->getValue()),
                    'graduation_announce_event' => $sheet->getCell("R{$row}")->getValue() ?: null,
                    'graduation_date' => $graduationDate,
                    'status' => $graduationDate ? 'Lulus' : 'Aktif',
                ]
            );

            // Sync singles
            $singleSync = [];
            foreach ($singleColumns as $col => $singleId) {
                $val = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                if (! $val) continue;
                $val = strtolower(trim((string) $val));
                if ($val === 'c') {
                    $singleSync[$singleId] = ['role' => 'center'];
                } elseif ($val === 'v') {
                    $singleSync[$singleId] = ['role' => 'member'];
                }
            }
            if (! empty($singleSync)) {
                $member->singles()->sync($singleSync);
            }

            $count++;
        }

        $this->command->info("Seeded {$count} members.");
    }

    private function seedCaptains($spreadsheet): void
    {
        $sheet = $spreadsheet->getSheetByName('Kapten');
        if (! $sheet) return;

        $count = 0;
        for ($row = 2; $row <= 100; $row++) {
            $name = $sheet->getCell("A{$row}")->getValue();
            if (! $name) break;

            $member = Member::where('name', 'like', "%{$name}%")->first();
            if (! $member) continue;

            $startDate = $this->excelDate($sheet->getCell("B{$row}")->getValue());
            $endDateVal = $sheet->getCell("C{$row}")->getValue();
            $endDate = is_string($endDateVal) && str_contains($endDateVal, 'TODAY')
                ? null
                : $this->excelDate($endDateVal);
            $position = $sheet->getCell("E{$row}")->getValue();

            if (! $startDate || ! $position) continue;

            Captain::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'position' => $position,
                    'start_date' => $startDate,
                ],
                [
                    'end_date' => $endDate,
                ]
            );
            $count++;
        }

        $this->command->info("Seeded {$count} captains.");
    }

    private function excelDate($value): ?string
    {
        if (! $value) return null;

        // Numeric Excel date
        if (is_numeric($value)) {
            try {
                $ts = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float) $value);
                return Carbon::createFromTimestamp($ts)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // DateTime object
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // String
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
