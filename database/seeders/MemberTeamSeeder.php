<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberTeam;
use App\Models\Team;
use Illuminate\Database\Seeder;

/**
 * Riwayat keanggotaan tim (member_teams).
 *
 * Sumber data: akb48.fandom.com — History: Team J / Team KIII / Team T /
 * Team Love / Team Dream / Team Passion (per 2 September 2026).
 *
 * Aturan:
 * - Setiap baris = satu "stint" (member di tim tertentu, dari joined_date
 *   sampai left_date). Jika member pindah tim lalu kembali, itu dua baris.
 * - Posisi konkuran (concurrent) dicatat sebagai baris terpisah pada tim
 *   sekunder, dengan notes "Posisi konkuran".
 * - Untuk stint yang berakhir karena tim dibubarkan (restrukturisasi 2021),
 *   left_date = tanggal senshuuraku stage tim tersebut:
 *     Team T   = 2021-03-12
 *     Team KIII = 2021-03-13
 *     Team J   = 2021-03-14
 * - Untuk Team Love/Dream/Passion (2026), joined_date = 2026-04-01
 *   (tanggal tim resmi terbentuk; pengumuman formasi 2025-12-20).
 */
class MemberTeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::pluck('id', 'code')->all();

        $rows = array_merge(
            $this->teamJRows(),
            $this->teamKIIIRows(),
            $this->teamTRows(),
            $this->teamLoveRows(),
            $this->teamDreamRows(),
            $this->teamPassionRows(),
        );

        $inserted = 0;
        $skipped = [];

        foreach ($rows as $row) {
            $member = Member::where('name', $row['member'])->first();
            if (! $member) {
                $skipped[] = $row['member'];
                continue;
            }

            $teamId = $teams[$row['team']] ?? null;
            if (! $teamId) {
                $this->command->warn("Team code tidak ditemukan: {$row['team']}");
                continue;
            }

            MemberTeam::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'team_id' => $teamId,
                    'joined_date' => $row['joined_date'],
                ],
                [
                    'left_date' => $row['left_date'] ?? null,
                    'notes' => $row['notes'] ?? null,
                ]
            );
            $inserted++;
        }

        foreach (array_unique($skipped) as $name) {
            $this->command->warn("Member tidak ditemukan di DB: {$name}");
        }

        $this->command->info("Seeded {$inserted} member_teams records.");
    }

    /**
     * Helper: ubah baris kompak [name, joined, left|null, notes|null]
     * menjadi array asosiatif dengan team code.
     */
    private function stints(string $team, array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'team' => $team,
                'member' => $r[0],
                'joined_date' => $r[1],
                'left_date' => $r[2] ?? null,
                'notes' => $r[3] ?? null,
            ];
        }
        return $out;
    }

    // =====================================================================
    // Team J (1st Team) - 2012-12-23 sampai 2021-03-14
    // =====================================================================
    private function teamJRows(): array
    {
        return $this->stints('J', [
            // ----- Formasi awal 24 member (JKT48 1st Anniversary Event) -----
            ['Aki Takajo',                     '2012-12-23', '2014-02-24', 'Formasi awal. Konkuran AKB48 Team B sejak 2013-04-28; transfer penuh ke AKB48 pada 2014-02-24 (Daisokaku Matsuri).'],
            ['Alissa Galliamova',              '2012-12-23', '2013-01-21', 'Formasi awal. Lulus 2013-01-21.'],
            ['Ayana Shahab',                   '2012-12-23', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Beby Chaesara Anadila',          '2012-12-23', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Cindy Gulla',                    '2012-12-23', '2014-02-17', 'Formasi awal. Dismissed 2014-02-17.'],
            ['Delima Rizky',                   '2012-12-23', '2016-03-18', 'Formasi awal. Dismissed 2016-03-18.'],
            ['Devi Kinal Putri',               '2012-12-23', '2015-06-13', 'Formasi awal, kapten Tim J pertama. Transfer ke Team KIII sebagai kapten (shuffle "Ada Banyak Rasa").'],
            ['Diasta Priswarini',              '2012-12-23', '2013-12-22', 'Formasi awal. Lulus 2013-12-22.'],
            ['Frieska Anastasia Laksani',      '2012-12-23', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Gabriela Margareth Warouw',      '2012-12-23', '2021-03-14', 'Formasi awal. Kapten Tim J 2018-07-01 sd 2020-06-06. Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Ghaida Farisya',                 '2012-12-23', '2016-11-20', 'Formasi awal. Lulus 2016-11-20.'],
            ['Haruka Nakagawa',                '2012-12-23', '2015-06-13', 'Formasi awal. Transfer ke Team T sebagai kapten (shuffle "Ada Banyak Rasa").'],
            ['Jessica Vania',                  '2012-12-23', '2017-03-12', 'Formasi awal. Lulus 2017-03-12.'],
            ['Jessica Veranda Tanumihardja',   '2012-12-23', '2017-05-25', 'Formasi awal. Lulus 2017-05-25.'],
            ['Melody Nurramdhani Laksani',     '2012-12-23', '2018-03-31', 'Formasi awal, center. Kapten JKT48 2013-12-21 sd 2018-03-31. Konkuran Team T (2016-09-11 sd 2018-03-31). Lulus 2018-03-31.'],
            ['Nabilah Ratna Ayu Azalia',       '2012-12-23', '2017-10-31', 'Formasi awal. Resign 2017-10-31.'],
            ['Rena Nozawa',                    '2012-12-23', '2014-02-24', 'Formasi awal. Konkuran AKB48 Team K sejak 2013-06-18; transfer penuh ke AKB48 pada 2014-02-24.'],
            ['Rezky Wiranti Dhike',            '2012-12-23', '2016-09-13', 'Formasi awal. Lulus 2016-09-13.'],
            ['Rica Leyona',                    '2012-12-23', '2014-12-04', 'Formasi awal. Resign 2014-12-04.'],
            ['Sendy Ariani',                   '2012-12-23', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Shania Junianatha',              '2012-12-23', '2019-04-28', 'Formasi awal. Kapten Tim J 2015-08-01 sd 2018-03-31; kapten JKT48 2018-04-01 sd 2019-04-27. Lulus 2019-04-28.'],
            ['Sonia Natalia',                  '2012-12-23', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Sonya Pandarmawan',              '2012-12-23', '2013-12-22', 'Formasi awal. Lulus 2013-12-22.'],
            ['Stella Cornelia',                '2012-12-23', '2013-12-28', 'Formasi awal. Lulus 2013-12-28.'],

            // ----- Promosi/transfer masuk (2014-2015) -----
            ['Jennifer Rachel Natasya',        '2014-02-23', '2016-09-11', 'Promosi ke Tim J (Manatsu no Sounds Good! Handshake). Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Thalia Ivanka Elizabeth',        '2014-02-23', '2018-05-27', 'Promosi ke Tim J (Manatsu no Sounds Good! Handshake). Transfer ke Team T (RE:BOOST).'],
            ['Dena Siti Rohyati',              '2014-05-18', '2017-12-01', 'Promosi ke Tim J (Flying Get Handshake). Demoted ke Trainee 2017-12-01.'],
            ['Sofia Meifaliani',               '2015-06-13', '2016-04-27', 'Promosi ke Tim J. Lulus 2016-04-27.'],
            ['Shinta Naomi',                   '2015-06-13', '2016-09-11', 'Transfer dari Team KIII. Transfer kembali ke Team KIII (1st Grand Team Shuffle).'],
            ['Elaine Hartanto',                '2015-06-13', '2016-04-03', 'Transfer dari Team T. Lulus 2016-04-03.'],

            // ----- 1st Grand Team Shuffle 2016-09-11 (masuk ke J) -----
            ['Della Delila',                   '2016-09-11', '2019-03-31', 'Transfer dari Team KIII (1st Grand Team Shuffle). Lulus 2019-03-31.'],
            ['Devi Kinal Putri',               '2016-09-11', '2018-06-30', 'Transfer kembali dari Team KIII (1st Grand Team Shuffle). Lulus 2018-06-30.'],
            ['Dwi Putri Bonita',               '2016-09-11', '2018-09-30', 'Transfer dari Team KIII (1st Grand Team Shuffle). Lulus 2018-09-30.'],
            ['Feni Fitriyanti',                '2016-09-11', '2021-03-14', 'Transfer dari Team T (1st Grand Team Shuffle). Center Tim J sejak 2019-07-21. Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Haruka Nakagawa',                '2016-09-11', '2016-12-30', 'Transfer kembali dari Team T (1st Grand Team Shuffle). Lulus 2016-12-30.'],
            ['Michelle Christo Kusnadi',       '2016-09-11', '2020-01-18', 'Transfer dari Team T (1st Grand Team Shuffle). Lulus 2020-01-18.'],
            ['Nadhifa Salsabila',              '2016-09-11', '2017-02-16', 'Transfer dari Team T (1st Grand Team Shuffle). Resign 2017-02-16.'],
            ['Priscillia Sari Dewi',           '2016-09-11', '2018-06-07', 'Transfer dari Team KIII (1st Grand Team Shuffle). Kapten Tim J 2018-04-01 sd 2018-06-07 (reinstated & demoted ke Academy Class A).'],
            ['Riskha Fairunissa',              '2016-09-11', '2018-12-29', 'Transfer dari Team KIII (1st Grand Team Shuffle). Lulus 2018-12-29.'],
            ['Saktia Oktapyani',               '2016-09-11', '2019-03-31', 'Transfer dari Team T (1st Grand Team Shuffle). Lulus 2019-03-31.'],
            ['Sinka Juliani',                  '2016-09-11', '2019-10-05', 'Transfer dari Team KIII (1st Grand Team Shuffle). Lulus 2019-10-05.'],
            ['Syahfira Angela Nurhaliza',      '2016-09-11', '2018-05-27', 'Transfer dari Team T (1st Grand Team Shuffle). Transfer kembali ke Team T (RE:BOOST).'],
            ['Viviyona Apriani',               '2016-09-11', '2017-12-23', 'Transfer dari Team KIII (1st Grand Team Shuffle). Transfer kembali ke Team KIII sebagai kapten (JKT48 6th Birthday Party).'],
            ['Yansen Indiani',                 '2016-09-11', '2017-03-02', 'Transfer dari Team T (1st Grand Team Shuffle). Resign 2017-03-02.'],

            // ----- 2017 -----
            ['Sri Lintang',                    '2017-03-04', '2017-12-07', 'Transfer dari Team T (Saikou ka yo Handshake). Resign 2017-12-07.'],
            ['Zahra Yuriva Dermawan',          '2017-03-04', '2018-02-10', 'Transfer dari Team T (Saikou ka yo Handshake). Resign 2018-02-10.'],
            ['Cindy Hapsari Maharani Pujiantoro Putri', '2017-12-23', '2021-03-14', 'Transfer dari Team T (JKT48 6th Birthday Party). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Nurhayati',                      '2017-12-23', '2019-04-27', 'Transfer dari Team T (JKT48 6th Birthday Party). Transfer ke Team KIII (Request Hour 2019).'],
            ['Stephanie Pricilla Indarto Putri', '2017-12-23', '2019-04-11', 'Transfer dari Team KIII (JKT48 6th Birthday Party). Demoted ke Academy Class A pada 2019-04-11.'],

            // ----- RE:BOOST 2018-05-27 (masuk ke J) -----
            ['Frieska Anastasia Laksani',      '2018-05-27', '2021-02-20', 'Transfer kembali dari Team KIII (RE:BOOST). Kapten Tim J 2020-06-06 sd graduation. Lulus 2021-02-20.'],
            ['Cindy Yuvia',                    '2018-05-27', '2019-07-27', 'Transfer dari Team KIII (RE:BOOST). Center Tim J. Lulus 2019-07-27.'],
            ['Sania Julia Montolalu',          '2018-05-27', '2021-03-14', 'Promosi ke Tim J (RE:BOOST). Team dibubarkan; lulus.'],

            // ----- 2018-2019 promosi -----
            ['Ariella Calista Ichwan',         '2018-09-29', '2021-03-14', 'Promosi ke Tim J (Everyday Katyusha / UZA Handshake). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Diani Amalia Ramadhani',         '2018-09-29', '2021-03-14', 'Promosi ke Tim J (Everyday Katyusha / UZA Handshake). Team dibubarkan; lulus.'],
            ['Eve Antoinette Ichwan',          '2019-03-30', '2021-03-14', 'Promosi ke Tim J (High Tension Handshake), setelah sebelumnya demoted dari Team T (2018-11-07). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Riska Amelia Putri',             '2019-03-30', '2021-03-14', 'Promosi ke Tim J (High Tension Handshake). Team dibubarkan; lulus.'],

            // ----- Request Hour 2019 (2019-04-27) -----
            ['Rona Anggreani',                 '2019-04-27', '2021-02-13', 'Transfer dari Team KIII (Request Hour 2019). Lulus 2021-02-13.'],
            ['Fransisca Saraswati Puspa Dewi', '2019-04-27', '2021-03-14', 'Transfer dari Team KIII (Request Hour 2019). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Nadila Cindi Wantari',           '2019-04-27', '2021-02-07', 'Transfer dari Team KIII (Request Hour 2019). Lulus 2021-02-07.'],
            ['Ni Made Ayu Vania Aurellia',     '2019-04-27', '2020-12-13', 'Transfer dari Team KIII (Request Hour 2019). Resign 2020-12-13.'],

            // ----- 2019-10-05 promosi -----
            ['Aurel Mayori',                   '2019-10-05', '2020-05-26', 'Promosi ke Tim J (Idol no Yoake Handshake). Transfer ke Team T (Team T 3rd Formation).'],
            ['Azizi Asadel',                   '2019-10-05', '2021-03-14', 'Promosi ke Tim J (Idol no Yoake Handshake). Team dibubarkan; transfer ke JKT48 New Formation.'],

            // ----- Team T 2nd Formation Disbandment 2019-12-22 (masuk ke J) -----
            ['Adriani Elisabeth',              '2019-12-22', '2021-03-14', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; lulus.'],
            ['Gabriel Angelina',               '2019-12-22', '2021-03-14', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; lulus.'],
            ['Nabila Fitriana',                '2019-12-22', '2021-03-14', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; lulus.'],
            ['Melati Putri Rahel Sesilia',     '2019-12-22', '2020-10-24', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Lulus 2020-10-24.'],
            ['Tan Zhi Hui Celine',             '2019-12-22', '2020-06-04', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Transfer kembali ke Team T sebagai kapten pada 2020-06-04.'],
            ['Aninditha Rahma Cahyadi',        '2019-12-22', '2021-03-14', 'Posisi konkuran dari Team KIII (primary). Konkuran Team T sebelumnya (2019-04-27 sd 2019-12-22) direalokasi ke Team J. Team dibubarkan.'],

            // ----- 2020 -----
            ['Amanina Afiqah',                 '2020-06-04', '2021-03-14', 'Promosi ke Tim J. Team dibubarkan; lulus.'],
        ]);
    }

    // =====================================================================
    // Team KIII (2nd Team) - 2013-06-25 sampai 2021-03-13
    // =====================================================================
    private function teamKIIIRows(): array
    {
        return $this->stints('KIII', [
            // ----- Formasi awal 18 member -----
            ['Alicia Chanzia',                 '2013-06-25', '2019-08-04', 'Formasi awal. Lulus 2019-08-04.'],
            ['Cindy Yuvia',                    '2013-06-25', '2018-05-27', 'Formasi awal. Transfer ke Team J (RE:BOOST).'],
            ['Della Delila',                   '2013-06-25', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Dwi Putri Bonita',               '2013-06-25', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Intar Putri Kariina',            '2013-06-25', '2013-09-10', 'Formasi awal. Lulus 2013-09-10.'],
            ['Jennifer Hanna',                 '2013-06-25', '2016-09-27', 'Formasi awal. Lulus 2016-09-27.'],
            ['Lidya Maulida Djuhandar',        '2013-06-25', '2018-10-27', 'Formasi awal. Lulus 2018-10-27.'],
            ['Nadila Cindi Wantari',           '2013-06-25', '2019-04-27', 'Formasi awal. Transfer ke Team J (Request Hour 2019).'],
            ['Natalia',                        '2013-06-25', '2019-07-20', 'Formasi awal. Lulus 2019-07-20.'],
            ['Noella Sisterina',               '2013-06-25', '2015-02-27', 'Formasi awal. Lulus 2015-02-27.'],
            ['Octi Sevpin',                    '2013-06-25', '2014-01-23', 'Formasi awal. Lulus 2014-01-23.'],
            ['Ratu Vienny Fitrilya',           '2013-06-25', '2017-10-12', 'Formasi awal. Kapten Tim KIII 2016-12-01 sd 2017-10-12 (reinstated & demoted ke Trainee).'],
            ['Riskha Fairunissa',              '2013-06-25', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Rona Anggreani',                 '2013-06-25', '2019-04-27', 'Formasi awal. Transfer ke Team J (Request Hour 2019).'],
            ['Shinta Naomi',                   '2013-06-25', '2015-06-13', 'Formasi awal, kapten Tim KIII pertama (2013-07-03 sd 2015-08-01). Transfer ke Team J (shuffle "Ada Banyak Rasa").'],
            ['Sinka Juliani',                  '2013-06-25', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Thalia',                         '2013-06-25', '2015-08-26', 'Formasi awal. Resign 2015-08-26.'],
            ['Viviyona Apriani',               '2013-06-25', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],

            // ----- Promosi 2014 -----
            ['Fakhriyani Shafariyanti',        '2014-05-18', '2018-03-25', 'Promosi ke Tim KIII. Lulus 2018-03-25.'],
            ['Novinta Dhini',                  '2014-05-18', '2015-08-17', 'Promosi ke Tim KIII. Lulus 2015-08-17.'],
            ['Priscillia Sari Dewi',           '2014-05-18', '2016-09-11', 'Promosi ke Tim KIII. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Saktia Oktapyani',               '2014-05-18', '2016-09-11', 'Promosi ke Tim KIII. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Rina Chikano',                   '2014-06-11', '2018-03-25', 'Promosi ke Tim KIII. Lulus 2018-03-25.'],

            // ----- Shuffle 2015-06-13 -----
            ['Devi Kinal Putri',               '2015-06-13', '2016-09-11', 'Transfer dari Team J sebagai kapten (shuffle "Ada Banyak Rasa"). Kapten Tim KIII 2015-08-01 sd 2016-11-30. Transfer kembali ke Team J (1st Grand Team Shuffle).'],

            // ----- 1st Grand Team Shuffle 2016-09-11 (masuk ke KIII) -----
            ['Ayana Shahab',                   '2016-09-11', '2018-05-27', 'Transfer dari Team J (1st Grand Team Shuffle). Konkuran Team T (2017-12-23 sd 2018-05-27); transfer penuh ke Team T (RE:BOOST).'],
            ['Beby Chaesara Anadila',          '2016-09-11', '2021-02-21', 'Transfer dari Team J (1st Grand Team Shuffle). Lulus 2021-02-21.'],
            ['Frieska Anastasia Laksani',      '2016-09-11', '2018-05-27', 'Transfer dari Team J (1st Grand Team Shuffle). Transfer kembali ke Team J (RE:BOOST).'],
            ['Sendy Ariani',                   '2016-09-11', '2016-12-01', 'Transfer dari Team J (1st Grand Team Shuffle). Dismissed 2016-12-01.'],
            ['Sonia Natalia',                  '2016-09-11', '2018-05-27', 'Transfer dari Team J (1st Grand Team Shuffle). Transfer ke Team T (RE:BOOST).'],
            ['Shinta Naomi',                   '2016-09-11', '2018-12-29', 'Transfer kembali dari Team J (1st Grand Team Shuffle). Lulus 2018-12-29.'],
            ['Jennifer Rachel Natasya',        '2016-09-11', '2020-01-05', 'Transfer dari Team J (1st Grand Team Shuffle). Lulus 2020-01-05.'],
            ['Amanda Dwi Arista',              '2016-09-11', '2017-12-01', 'Transfer dari Team T (1st Grand Team Shuffle). Demoted ke Trainee 2017-12-01.'],
            ['Aninditha Rahma Cahyadi',        '2016-09-11', '2021-03-13', 'Transfer dari Team T (1st Grand Team Shuffle). Konkuran Team T (2019-04-27 sd 2019-12-22), konkuran Team J (2019-12-22 sd 2021-03-14), konkuran ganda J+T sejak 2020-05-31. Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Ayu Safira Oktaviani',           '2016-09-11', '2017-12-01', 'Transfer dari Team T (1st Grand Team Shuffle). Demoted ke Trainee 2017-12-01.'],
            ['Fransisca Saraswati Puspa Dewi', '2016-09-11', '2019-04-27', 'Transfer dari Team T (1st Grand Team Shuffle). Transfer ke Team J (Request Hour 2019).'],
            ['Maria Genoveva Natalia Desy Purnamasari Gunawan', '2016-09-11', '2020-12-26', 'Transfer dari Team T (1st Grand Team Shuffle). Lulus 2020-12-26.'],
            ['Ni Made Ayu Vania Aurellia',     '2016-09-11', '2019-04-27', 'Transfer dari Team T (1st Grand Team Shuffle). Transfer ke Team J (Request Hour 2019).'],
            ['Shani Indira Natio',             '2016-09-11', '2021-03-13', 'Transfer dari Team T (1st Grand Team Shuffle). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Shania Gracia',                  '2016-09-11', '2021-03-13', 'Transfer dari Team T (1st Grand Team Shuffle). Center Tim KIII sejak RE:BOOST 2018-05-27. Kapten Tim KIII 2020-06-06 sd 2021-03-13. Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Stephanie Pricilla Indarto Putri', '2016-09-11', '2017-12-23', 'Transfer dari Team T (1st Grand Team Shuffle). Transfer ke Team J (JKT48 6th Birthday Party).'],

            // ----- 2018 -----
            ['Ratu Vienny Fitrilya',           '2018-02-23', '2020-02-23', 'Re-promoted ke Tim KIII. Kapten Tim KIII 2019-12-22 sd graduation. Lulus 2020-02-23.'],
            ['Erika Ebisawa Kuswan',           '2018-09-29', '2019-02-09', 'Promosi ke Tim KIII (Everyday Katyusha / UZA Handshake). Resign 2019-02-09.'],
            ['Kandiya Rafa Maulidita',         '2018-12-22', '2021-03-13', 'Promosi ke Tim KIII (JKT48 7th Anniversary). Team dibubarkan; lulus.'],

            // ----- Request Hour 2019 (2019-04-27) -----
            ['Anastasya Narwastu Tety Handuran', '2019-04-27', '2021-03-13', 'Promosi ke Tim KIII (Request Hour 2019). Team dibubarkan; lulus.'],
            ['Gita Sekar Andarini',            '2019-04-27', '2021-03-13', 'Promosi ke Tim KIII (Request Hour 2019). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Angelina Christy',               '2019-04-27', '2021-03-13', 'Promosi ke Tim KIII (Request Hour 2019). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Yessica Tamara',                 '2019-04-27', '2021-03-13', 'Promosi ke Tim KIII (Request Hour 2019). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Nurhayati',                      '2019-04-27', '2021-03-13', 'Transfer dari Team J (Request Hour 2019). Team dibubarkan; lulus.'],

            // ----- 2019 lainnya -----
            ['Helisma Putri',                  '2019-07-21', '2021-03-13', 'Promosi ke Tim KIII (J Paradise & Cindy Yuvia Graduation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Mutiara Azzahra',                '2019-07-21', '2021-03-13', 'Promosi ke Tim KIII (J Paradise & Cindy Yuvia Graduation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Stephanie Pricilla Indarto Putri', '2019-09-10', '2019-09-28', 'Re-promoted ke Tim KIII. Lulus 2019-09-28.'],

            // ----- Team T Disbandment 2019-12-22 (masuk ke KIII) -----
            ['Fidly Immanda Azzahra',          '2019-12-22', '2021-03-13', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; lulus.'],
            ['Gabryela Marcelina',             '2019-12-22', '2020-11-16', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Dismissed 2020-11-16.'],
            ['Jinan Safa Safira',              '2019-12-22', '2021-03-13', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Puti Nadhira Azalia',            '2019-12-22', '2020-05-15', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Lulus 2020-05-15.'],
            ['Rinanda Syahputri',              '2019-12-22', '2021-03-13', 'Transfer dari Team T (Team T disbandment - 8th Anniversary). Team dibubarkan; lulus.'],

            // ----- 2020 -----
            ['Zahra Nur',                      '2020-06-04', '2021-03-13', 'Promosi ke Tim KIII. Team dibubarkan; lulus 2021-08-25.'],
        ]);
    }

    // =====================================================================
    // Team T (3rd Team) - 2015-01-24 sampai 2021-03-12
    // =====================================================================
    private function teamTRows(): array
    {
        return $this->stints('T', [
            // ----- Formasi awal 16 member (2015-01-24) -----
            ['Amanda Dwi Arista',              '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Andela Yuwono',                  '2015-01-24', '2015-09-04', 'Formasi awal (Center). Resign 2015-09-04.'],
            ['Aninditha Rahma Cahyadi',        '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Ayu Safira Oktaviani',           '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Chikita Ravenska Mamesah',       '2015-01-24', '2016-05-29', 'Formasi awal. Lulus 2016-05-29.'],
            ['Elaine Hartanto',                '2015-01-24', '2015-06-13', 'Formasi awal. Transfer ke Team J (shuffle "Ada Banyak Rasa").'],
            ['Feni Fitriyanti',                '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Fransisca Saraswati Puspa Dewi', '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Maria Genoveva Natalia Desy Purnamasari Gunawan', '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Martha Graciela',                '2015-01-24', '2016-04-22', 'Formasi awal. Lulus 2016-04-22.'],
            ['Michelle Christo Kusnadi',       '2015-01-24', '2016-09-11', 'Formasi awal (Center). Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Nadhifa Salsabila',              '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],
            ['Ni Made Ayu Vania Aurellia',     '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Shani Indira Natio',             '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Shania Gracia',                  '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Syahfira Angela Nurhaliza',      '2015-01-24', '2016-09-11', 'Formasi awal. Transfer ke Team J (1st Grand Team Shuffle).'],

            // ----- Shuffle 2015-06-13 -----
            ['Haruka Nakagawa',                '2015-06-13', '2016-09-11', 'Transfer dari Team J sebagai kapten (shuffle "Ada Banyak Rasa"). Kapten Tim T 2015-08-01 sd 2016-11-30. Transfer kembali ke Team J (1st Grand Team Shuffle).'],
            ['Nina Hamidah',                   '2015-06-13', '2016-02-01', 'Promosi ke Tim T. Resign 2016-02-01.'],
            ['Stephanie Pricilla Indarto Putri', '2015-06-13', '2016-09-11', 'Promosi ke Tim T. Transfer ke Team KIII (1st Grand Team Shuffle).'],
            ['Yansen Indiani',                 '2015-06-13', '2016-09-11', 'Promosi ke Tim T. Transfer ke Team J (1st Grand Team Shuffle).'],

            // ----- 1st Grand Team Shuffle 2016-09-11 (masuk ke T) -----
            ['Adhisty Zara',                   '2016-09-11', '2019-12-04', 'Promosi ke Tim T (1st Grand Team Shuffle). Center Tim T sejak RE:BOOST 2018-05-27. Lulus 2019-12-04.'],
            ['Adriani Elisabeth',              '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (Team T disbandment).'],
            ['Christi',                        '2016-09-11', '2017-10-21', 'Promosi ke Tim T (1st Grand Team Shuffle). Lulus 2017-10-21.'],
            ['Cindy Hapsari Maharani Pujiantoro Putri', '2016-09-11', '2017-12-23', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (JKT48 6th Birthday Party).'],
            ['Elizabeth Gloria Setiawan',      '2016-09-11', '2018-04-13', 'Promosi ke Tim T (1st Grand Team Shuffle). Resign 2018-04-13.'],
            ['Eve Antoinette Ichwan',          '2016-09-11', '2018-11-07', 'Promosi ke Tim T (1st Grand Team Shuffle). Demoted ke Academy Class A 2018-11-07.'],
            ['Fidly Immanda Azzahra',          '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team KIII (Team T disbandment).'],
            ['Jinan Safa Safira',              '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team KIII (Team T disbandment).'],
            ['Made Devi Ranita Ningtara',      '2016-09-11', '2018-12-27', 'Promosi ke Tim T (1st Grand Team Shuffle). Lulus 2018-12-27.'],
            ['Melati Putri Rahel Sesilia',     '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (Team T disbandment).'],
            ['Melody Nurramdhani Laksani',     '2016-09-11', '2018-03-31', 'Posisi konkuran (primary: Team J). Kapten Tim T 2016-12-01 sd 2018-03-31. Lulus 2018-03-31.'],
            ['Nurhayati',                      '2016-09-11', '2017-12-23', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (JKT48 6th Birthday Party).'],
            ['Puti Nadhira Azalia',            '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team KIII (Team T disbandment).'],
            ['Regina Angelina',                '2016-09-11', '2017-11-01', 'Promosi ke Tim T (1st Grand Team Shuffle). Dismissed 2017-11-01.'],
            ['Ruth Damayanti Sitanggang',      '2016-09-11', '2018-06-30', 'Promosi ke Tim T (1st Grand Team Shuffle). Resign 2018-06-30.'],
            ['Sri Lintang',                    '2016-09-11', '2017-03-04', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (Saikou ka yo Handshake).'],
            ['Tan Zhi Hui Celine',             '2016-09-11', '2019-12-22', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (Team T disbandment).'],
            ['Violeta Burhan',                 '2016-09-11', '2018-07-03', 'Promosi ke Tim T (1st Grand Team Shuffle). Dismissed 2018-07-03.'],
            ['Zahra Yuriva Dermawan',          '2016-09-11', '2017-03-04', 'Promosi ke Tim T (1st Grand Team Shuffle). Transfer ke Team J (Saikou ka yo Handshake).'],

            // ----- 2017 -----
            ['Citra Ayu Pranajaya Wibrado',    '2017-03-04', '2018-06-30', 'Promosi ke Tim T (Saikou ka yo Handshake). Resign 2018-06-30.'],
            ['Ayana Shahab',                   '2017-12-23', '2019-12-08', 'Awal sebagai konkuran (primary: Team KIII). Transfer penuh ke Team T dan menjadi kapten pada RE:BOOST 2018-05-27. Lulus 2019-12-08.'],
            ['Hasyakyla Utami Kusumawardhani', '2017-12-23', '2019-11-10', 'Promosi ke Tim T (JKT48 6th Birthday Party). Lulus 2019-11-10.'],

            // ----- RE:BOOST 2018-05-27 (masuk ke T) -----
            ['Ayu Safira Oktaviani',           '2018-05-27', '2019-04-23', 'Re-promoted ke Tim T (RE:BOOST). Resign 2019-04-23.'],
            ['Sonia Natalia',                  '2018-05-27', '2019-12-07', 'Transfer dari Team KIII (RE:BOOST). Co-captain Tim T sejak 2018-07-15. Lulus 2019-12-07.'],
            ['Syahfira Angela Nurhaliza',      '2018-05-27', '2019-12-13', 'Transfer kembali dari Team J (RE:BOOST). Lulus 2019-12-13.'],
            ['Thalia Ivanka Elizabeth',        '2018-05-27', '2019-12-06', 'Transfer dari Team J (RE:BOOST). Lulus 2019-12-06.'],

            // ----- 2018-2019 promosi -----
            ['Rinanda Syahputri',              '2018-09-29', '2019-12-22', 'Promosi ke Tim T (Everyday Katyusha / UZA Handshake). Transfer ke Team KIII (Team T disbandment).'],
            ['Gabryela Marcelina',             '2018-12-22', '2019-12-22', 'Promosi ke Tim T (JKT48 7th Anniversary). Transfer ke Team KIII (Team T disbandment).'],
            ['Gabriel Angelina',               '2019-04-27', '2019-12-22', 'Promosi ke Tim T (Request Hour 2019). Transfer ke Team J (Team T disbandment).'],
            ['Nabila Fitriana',                '2019-04-27', '2019-12-22', 'Promosi ke Tim T (Request Hour 2019). Transfer ke Team J (Team T disbandment).'],
            ['Aninditha Rahma Cahyadi',        '2019-04-27', '2019-12-22', 'Posisi konkuran (primary: Team KIII, Request Hour 2019). Konkuran Team T dicabut pada Team T disbandment; direalokasi ke Team J.'],

            // ----- Team T 3rd Formation (2020-05-26 sd 2020-06-04, aktif 2020-08-22) -----
            ['Aurel Mayori',                   '2020-05-26', '2021-03-12', 'Transfer dari Team J (Team T 3rd Formation). Team dibubarkan; lulus 2021-03-12.'],
            ['Nyimas Ratu Rafa',               '2020-05-27', '2020-11-09', 'Promosi ke Tim T (Team T 3rd Formation). Resign 2020-11-09.'],
            ['Umega Maulana',                  '2020-05-28', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; lulus 2021-03-12.'],
            ['Dhea Angelia',                   '2020-05-28', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan.'],
            ['Reva Fidela',                    '2020-05-29', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan.'],
            ['Lulu Salsabila',                 '2020-05-29', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Flora Shafiq',                   '2020-05-30', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan.'],
            ['Jessica Chandra',                '2020-05-30', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Jesslyn Callista',               '2020-05-31', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan.'],
            ['Amirah Fatin',                   '2020-05-31', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Aninditha Rahma Cahyadi',        '2020-05-31', '2021-03-12', 'Posisi konkuran (primary: KIII, sekaligus konkuran J). Team dibubarkan.'],
            ['Cornelia Vanisa',                '2020-06-01', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Febriola Sinambela',             '2020-06-01', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Fiony Alveria',                  '2020-06-02', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Freya Jayawardana',              '2020-06-02', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; transfer ke JKT48 New Formation.'],
            ['Viona Fadrin',                   '2020-06-03', '2021-03-12', 'Promosi ke Tim T (Team T 3rd Formation). Team dibubarkan; lulus 2021-03-12.'],
            ['Tan Zhi Hui Celine',             '2020-06-04', '2021-03-12', 'Transfer kembali dari Team J sebagai kapten (Team T 3rd Formation). Team dibubarkan.'],
        ]);
    }

    // =====================================================================
    // Team Love (4th Team) - 2026-04-01
    // Diumumkan 2025-12-20 (The First Snow - JKT48 14th Anniversary Concert)
    // =====================================================================
    private function teamLoveRows(): array
    {
        $joined = '2026-04-01';

        return $this->stints('Love', [
            // Aktif
            ['Anindya Ramadhani Purnomo',     $joined, null, null],
            ['Cynthia Yaputera',              $joined, null, null],
            ['Celline Thefannie',             $joined, null, null],
            ['Fiony Alveria',                 $joined, null, null],
            ['Fritzy Rosmerian',              $joined, null, null],
            ['Grace Octaviani Tanujaya',      $joined, null, null],
            ['Indah Cahya',                   $joined, null, null],
            ['Aurhel Alana Tirta',            $joined, null, null],
            ['Aurellia',                      $joined, null, null],
            ['Hillary Abigail',               $joined, null, null],
            ['Michelle Alexandra Suandi',     $joined, null, null],
            ['Nayla Suji Aurelia',            $joined, null, null],
            ['Jazzlyn Agatha Trisha',         $joined, null, null],

            // Sudah/akan lulus
            ['Cathleen Hana Nixie',           $joined, '2026-08-15', 'Diumumkan lulus 2026-04-19, lulus 2026-08-15.'],
            ['Alya Amanda Fatihah',           $joined, null,         'Diumumkan lulus 2026-05-17, dijadwalkan lulus 2026-09-06.'],
        ]);
    }

    // =====================================================================
    // Team Dream (5th Team) - 2026-04-01
    // Amanda Sukma batal transfer (lulus 2026-03-29, sebelum tim terbentuk).
    // =====================================================================
    private function teamDreamRows(): array
    {
        $joined = '2026-04-01';

        return $this->stints('Dream', [
            // Aktif
            ['Adeline Wijaya',                $joined, null, null],
            ['Helisma Putri',                 $joined, null, null],
            ['Gabriela Abigail Mewengkang',   $joined, null, null],
            ['Freya Jayawardana',             $joined, null, null],
            ['Gita Sekar Andarini',           $joined, null, null],
            ['Greesella Sophina Adhalia',     $joined, null, null],
            ['Jesslyn Elly',                  $joined, null, null],
            ['Marsha Lenathea',               $joined, null, null],
            ['Nina Tutachia Chapman',         $joined, null, null],
            ['Shabilqis Naila Bustomi',       $joined, null, null],
            ['Oline Manuel Chay',             $joined, null, null],
            ['Febriola Sinambela',            $joined, null, null],

            // Sudah keluar
            ['Chelsea Davina Norman',         $joined, '2026-05-30', 'Diumumkan lulus 2026-04-01, lulus 2026-05-30.'],
            ['Gendis Mayrannisa Setiawan',    $joined, '2026-07-25', 'Kembali aktif 2026-05-18 setelah off, mengundurkan diri 2026-07-25.'],
        ]);
    }

    // =====================================================================
    // Team Passion (6th Team) - 2026-04-01
    // =====================================================================
    private function teamPassionRows(): array
    {
        $joined = '2026-04-01';

        return $this->stints('Passion', [
            ['Abigail Rachel Lie',              $joined, null, null],
            ['Angelina Christy',                $joined, null, null],
            ['Catherina Vallencia Kurniawan',   $joined, null, null],
            ['Desy Natalia Ang',                $joined, null, null],
            ['Dena Natalia Ang',                $joined, null, null],
            ['Jessica Chandra',                 $joined, null, null],
            ['Kathrina Irene',                  $joined, null, null],
            ['Victoria Kimberly Lukitama',      $joined, null, null],
            ['Michelle Levia Arifin',           $joined, null, null],
            ['Lulu Salsabila',                  $joined, null, null],
            ['Mutiara Azzahra',                 $joined, null, null],
            ['Cornelia Vanisa',                 $joined, null, null],
            ['Raisha Syifa Wardhana',           $joined, null, null],
            ['Ribka Budiman',                   $joined, null, null],

            // Feni hiatus sejak 2025-08-01 tapi tetap di roster.
            ['Feni Fitriyanti',                 $joined, null, 'Hiatus sejak 2025-08-01, masih tercatat di roster Team Passion.'],
        ]);
    }
}
