<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapelData = [
            [1, 'Matematika', 5, 'Teori'],
            [2, 'Bahasa Indonesia', 6, 'Teori'],
            [3, 'IPA', 5, 'Teori'],
            [4, 'IPS', 4, 'Teori'],
            [5, 'Bahasa Inggris', 4, 'Teori'],
            [6, 'PAI', 3, 'Teori'],
            [7, 'Pendidikan Pancasila', 3, 'Teori'],
            [8, 'PJOK', 3, 'Praktek'],
            [9, 'Seni Budaya dan Prakarya', 3, 'Teori'],
            [10, 'Informatika', 3, 'Teori'],
            [11, 'BK', 1, 'Teori']
        ];

        foreach ($mapelData as $data) {
            DB::table('mapel')->insert([
                'id_mapel' => $data[0],
                'nama_mapel' => $data[1],
                'jam_per_minggu' => $data[2],
                'kategori' => $data[3]
            ]);
        }
    }
}
