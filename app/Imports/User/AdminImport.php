<?php

namespace App\Imports\User;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class AdminImport implements ToModel, WithHeadingRow
{
    public int $insertedCount = 0;
    public function model(array $row)
    {
        Log::info('Import Row', $row);
        $nip = $row['nip'] ?? $row['NIP'] ?? null;
        $nama = $row['nama_lengkap'] ?? $row['Nama Lengkap'] ?? null;
        $password = $row['password'] ?? $row['Password'] ?? null;

        if(!$nip || !$nama || !$password) {
            return null;
        }

        $admin = new Admin([
            'nip' => trim($nip),
            'nama_lengkap' => trim($nama),
            'password' => bcrypt(trim($password))
        ]);

        $this->insertedCount++;
        return $admin;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
