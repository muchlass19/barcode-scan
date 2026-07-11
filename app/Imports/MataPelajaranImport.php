<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataPelajaranImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $nama = $row['nama_mata_pelajaran'] ?? $row['Nama Mata Pelajaran'] ?? null;

        if(!$nama) {
            return null;
        }

        return new MataPelajaran([
            'nama_mata_pelajaran' => $nama
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }
}
