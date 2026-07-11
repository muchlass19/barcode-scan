<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('guru')->insert([
            [
                'nip' => '1980010112345678',
                'nama_lengkap' => 'Budi Santoso',
                'password' => Hash::make('password123'),
            ],
            [
                'nip' => '1985120198765432',
                'nama_lengkap' => 'Siti Rahmawati',
                'password' => Hash::make('password123'),
            ],
        ]);
    }
}
