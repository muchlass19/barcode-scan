<?php

namespace App\Imports;

use App\Models\KelasSiswa;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KelasSiswaImport implements ToModel, WithHeadingRow
{
    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public int $insertedCount = 0;

    public function model(array $row)
    {
        Log::info('Import Row', $row);

        $nisn = $row['nisn'] ?? $row['NISN'] ?? null;
        $tahunAjaran = $row['tahun_ajaran'] ?? $row['Tahun Ajaran'] ?? null;

        // Validasi kolom wajib
        if (!$nisn || !$tahunAjaran) {
            Log::warning("❌ Baris dilewati: Kolom NISN atau Tahun Ajaran kosong.", $row);
            return null;
        }

        $nisn = trim($nisn);
        $tahunAjaran = trim($tahunAjaran);

        // Validasi: siswa ada atau tidak
        $siswa = Siswa::where('nisn', $nisn)->first();
        if (!$siswa) {
            Log::warning("❌ Siswa tidak ditemukan dalam database. NISN: {$nisn}");
            return null;
        }

        // Validasi: siswa sudah memiliki kelas atau belum (tahun ajaran tertentu)
        $sudahAda = KelasSiswa::where('siswa_nisn', $nisn)
            ->where('tahun_ajaran', $tahunAjaran)
            ->exists();

        if ($sudahAda) {
            Log::info("⚠️ Siswa sudah memiliki kelas pada tahun ajaran {$tahunAjaran}. NISN: {$nisn}");
            return null;
        }

        // Data valid → simpan
        $kelasSiswa = new KelasSiswa([
            'siswa_nisn' => $nisn,
            'kelas_id' => $this->kelasId,
            'tahun_ajaran' => $tahunAjaran
        ]);

        $this->insertedCount++;

        Log::info("✅ Siswa berhasil ditambahkan: NISN {$nisn}");
        return $kelasSiswa;
    }

    public function headingRow(): int
    {
        return 1;
    }
}
