<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([GuruSeeder::class,]);
        $this->call([MapelSeeder::class,]);
        $this->call([WaktuSeeder::class,]);
        $this->call([RuangSeeder::class,]);
        $this->call([KelasSeeder::class,]);
        $this->call([UserSeeder::class,]);
        $this->call([GuruMapelSeeder::class,]);
    }
}
