<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Workbook rekap absensi guru:
 * - "Rekap Absensi"   : detail per pertemuan (hadir & tidak hadir)
 * - "Rekap Kehadiran" : ringkasan jumlah hadir / tidak hadir per siswa
 */
class RekapAbsenExport implements WithMultipleSheets
{
    public function __construct(private readonly string $guruNip)
    {
    }

    public function sheets(): array
    {
        return [
            new RekapAbsenDetailSheet($this->guruNip),
            new RekapKehadiranSiswaSheet($this->guruNip),
        ];
    }
}