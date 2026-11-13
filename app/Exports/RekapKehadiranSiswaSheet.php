<?php

namespace App\Exports;

use App\Services\RekapAbsensiService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Sheet ringkasan: total hadir / tidak hadir / persentase per siswa.
 *
 * WithStrictNullComparison wajib agar nilai 0 tetap tertulis di sel.
 */
class RekapKehadiranSiswaSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStrictNullComparison, WithTitle
{
    public function __construct(private readonly string $guruNip)
    {
    }

    public function collection(): Collection
    {
        return collect(app(RekapAbsensiService::class)->summaryRows($this->guruNip));
    }

    public function headings(): array
    {
        return [
            'No',
            'Kelas',
            'Mata Pelajaran',
            'NISN',
            'Nama Siswa',
            'Hadir',
            'Tidak Hadir',
            'Total Pertemuan',
            'Persentase Kehadiran (%)',
        ];
    }

    public function title(): string
    {
        return 'Rekap Kehadiran';
    }
}
