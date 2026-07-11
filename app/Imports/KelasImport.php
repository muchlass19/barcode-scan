<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KelasImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public int $insertedCount = 0;

    public function model(array $row)
    {
        // Log::info('Import Row', $row);
        $nama = $row['nama_kelas'] ?? $row['Nama Kelas'] ?? null;
        $guru_nip = $row['nip_wali_kelas'] ?? $row['NIP Wali Kelas'] ?? null;

        if (!$nama || !$guru_nip) {
            return null;
        }

        $kelas = new Kelas([
            'nama_kelas' => trim($nama),
            'guru_nip' => trim($guru_nip),
        ]);

        $this->insertedCount++;
        return $kelas;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
