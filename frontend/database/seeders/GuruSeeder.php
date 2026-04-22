<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guruData = [
            [1, 'Afriwarman', '196512071995121001', 30, 'Matematika'],
            [2, 'Akmaluddin Mulis', '197402202005011005', 12, 'PJOK'],
            [3, 'Delviani Surya', '198807202023212035', 24, 'Bahasa Indonesia'],
            [4, 'Desriyati', '199002122023212032', 17, 'IPA, Informatika, Matematika'],
            [5, 'Efi Syuryani', '196712031998022001', 20, 'IPA'],
            [6, 'Eli Topia', '198406092014062009', 28, 'IPS'],
            [7, 'Faredna. Z', '199201052025212026', 24, 'Informatika'],
            [8, 'First Putri Yandi', '199307182023212027', 27, 'BK'],
            [9, 'Gusma Suci Ramadhani', '199403022023212025', 24, 'Bahasa Indonesia'],
            [10, 'Harni Yetti', '196704051991032005', 15, 'IPA'],
            [11, 'Husnijar Anshariah', '196801161991032005', 20, 'IPA'],
            [12, 'Irdanely', '196605091989032004', 24, 'Bahasa Inggris'],
            [13, 'IRWAN WAHYUDI', NULL, 16, 'Bahasa Inggris'],
            [14, 'Ivanny Saktya Octorina', NULL, 18, 'Bahasa Indonesia'],
            [15, 'Lamimi Agus', NULL, 27, 'Seni Budaya dan Prakarya'],
            [16, 'Lendriati', '197005012005012007', 12, 'Bahasa Indonesia'],
            [17, 'Leni Marlina M', '197707302005012005', 24, 'PAI'],
            [18, 'Levana Ariani', '198110072006042007', 15, 'IPA'],
            [19, 'LISA SUSANTI', NULL, 21, 'PAI'],
            [20, 'M.Iqbal', NULL, 21, 'PJOK'],
            [21, 'Marlis', '196608062008012003', 24, 'IPS'],
            [22, 'Marningsih', '197603262010012005', 21, 'Informatika'],
            [23, 'MUTHIA GUSTIANDI', '199608182023212015', 24, 'PJOK'],
            [24, 'Nadila', NULL, 18, 'BK'],
            [25, 'Nurdini', '196608252000122002', 24, 'IPA'],
            [26, 'PETRI MELDA DIANI', NULL, 27, 'Pendidikan Pancasila'],
            [27, 'Sastika Randra', NULL, 24, 'Bahasa Indonesia'],
            [28, 'Sri Ayu Ramadhani', NULL, 24, 'Seni Budaya dan Prakarya'],
            [29, 'Sumarni', '196701111989032001', 30, 'Matematika'],
            [30, 'Sylvia Eliza Azwar', '198809242017082002', 24, 'Bahasa Indonesia'],
            [31, 'Tati Tisnawati', '198205112005012016', 15, 'Matematika'],
            [32, 'WAIDIS', '198812312023211015', 24, 'PAI'],
            [33, 'Waslul Abral', '196905041997021003', 24, 'IPS'],
            [34, 'Yunimar', '196608262008012002', 30, 'Pendidikan Pancasila'],
            [35, 'Yusrita', '197506292005012006', 24, 'Bahasa Inggris']
        ];

        foreach ($guruData as $data) {
            DB::table('guru')->insert([
                'id_guru' => $data[0],
                'nama_guru' => $data[1],
                'nip' => $data[2],
                'jam_mingguan' => $data[3],
                'mapel' => $data[4]
            ]);
        }
    }
}
