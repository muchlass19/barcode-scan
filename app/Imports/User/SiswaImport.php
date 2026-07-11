<?php

namespace App\Imports\User;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow
{
    public int $insertedCount = 0;
    public function model(array $row)
    {
        Log::info('Import Row', $row);
        $nisn = $row['nisn'] ?? $row['NISN'] ?? null;
        $nama = $row['nama_lengkap'] ?? $row['Nama Lengkap'] ?? null;
        $password = $row['password'] ?? $row['Password'] ?? null;

        if(!$nisn || !$nama || !$password) {
            return null;
        }

        $siswa = new Siswa([
            'nisn' => trim($nisn),
            'nama_lengkap' => trim($nama),
            'password' => bcrypt(trim($password))
        ]);

        $this->insertedCount++;
        return $siswa;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
