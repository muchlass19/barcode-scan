<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('siswa')->insert([
            [
                'nisn' => '0056789012345678',
                'nama_lengkap' => 'Andi Wijaya',
                'password' => Hash::make('siswa123'),
            ],
            [
                'nisn' => '0098765432109876',
                'nama_lengkap' => 'Rina Marlina',
                'password' => Hash::make('siswa123'),
            ],
        ]);
    }
}
