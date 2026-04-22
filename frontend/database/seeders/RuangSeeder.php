<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruangData = [
            [1, 'Ruang TU dan Kepsek', 'ruangan', 35],
            [2, 'Lab Komputer 1', 'laboratorium', 40],
            [3, 'Lab Komputer 2', 'laboratorium', 40],
            [4, 'Ruang Majelis Guru', 'ruangan', 50],
            [5, 'Koperasi', 'kelas', 35],
            [6, 'Lab IPA', 'laboratorium', 50],
            [7, 'Ruang BK', 'ruangan', 35],
            [8, 'Ruang Olahraga', 'ruangan', 35],
            [9, 'Ruang Pramuka', 'ruangan', 35],
            [10, 'Ruang Kesenian', 'ruangan', 35],
            [11, 'Ruang UKS', 'ruangan', 35],
            [12, 'Kelas VII A', 'kelas', 35],
            [13, 'Kelas VII B', 'kelas', 35],
            [14, 'Kelas VII C', 'kelas', 35],
            [15, 'Kelas VII D', 'kelas', 35],
            [16, 'Kelas VII E', 'kelas', 35],
            [17, 'Kelas VII F', 'kelas', 35],
            [18, 'Kelas VIII A', 'kelas', 35],
            [19, 'Kelas VIII B', 'kelas', 35],
            [20, 'Kelas VIII C', 'kelas', 35],
            [21, 'Kelas VIII D', 'kelas', 35],
            [22, 'Kelas VIII E', 'kelas', 35],
            [23, 'Kelas VIII F', 'kelas', 35],
            [24, 'Kelas VIII G', 'kelas', 35],
            [25, 'Kelas IX A', 'kelas', 35],
            [26, 'Kelas IX B', 'kelas', 35],
            [27, 'Kelas IX C', 'kelas', 35],
            [28, 'Kelas IX D', 'kelas', 35],
            [29, 'Kelas IX E', 'kelas', 35],
            [30, 'Kelas IX F', 'kelas', 35]
        ];

        foreach ($ruangData as $data) {
            DB::table('ruang')->insert([
                'id_ruang' => $data[0],
                'nama_ruang' => $data[1],
                'tipe' => $data[2],
                'kapasitas' => $data[3],
                'mapel' => $data[4]
            ]);
        }
    }
}
