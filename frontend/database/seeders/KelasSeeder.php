<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasData = [
            [1, 'VII A', '7', 31, 29, 12],
            [2, 'VII B', '7', 32, 8, 13],
            [3, 'VII C', '7', 32, 32, 14],
            [4, 'VII D', '7', 28, 5, 15],
            [5, 'VII E', '7', 27, 21, 16],
            [6, 'VII F', '7', 25, 22, 17],
            [7, 'VIII A', '8', 32, 10, 18],
            [8, 'VIII B', '8', 31, 7, 19],
            [9, 'VIII C', '8', 30, 35, 20],
            [10, 'VIII D', '8', 27, 9, 21],
            [11, 'VIII E', '8', 27, 3, 22],
            [12, 'VIII F', '8', 26, 6, 23],
            [13, 'VIII G', '8', 25, 4, 24],
            [14, 'IX A', '9', 32, 34, 25],
            [15, 'IX B', '9', 32, 30, 26],
            [16, 'IX C', '9', 32, 25, 27],
            [17, 'IX D', '9', 31, 12, 28],
            [18, 'IX E', '9', 25, 23, 29],
            [19, 'IX F', '9', 25, 17, 30]
        ];

        foreach ($kelasData as $data) {
            DB::table('kelas')->insert([
                'id_kelas' => $data[0],
                'nama_kelas' => $data[1],
                'tingkat' => $data[2],
                'jumlah_siswa' => $data[3],
                'wali_kelas' => $data[4],
                'id_ruang' => $data[5],
            ]);
        }
    }
}
