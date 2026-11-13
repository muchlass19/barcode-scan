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
 * Sheet detail: satu baris per pertemuan per siswa (hadir & tidak hadir).
 *
 * WithStrictNullComparison wajib: tanpa itu nilai 0 (mis. jumlah jam/kolom
 * bernilai nol) dibuang oleh PhpSpreadsheet karena perbandingan longgar
 * terhadap null.
 */
class RekapAbsenDetailSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStrictNullComparison, WithTitle
{
    public function __construct(private readonly string $guruNip)
    {
    }

    public function collection(): Collection
    {
        return collect(app(RekapAbsensiService::class)->detailRows($this->guruNip));
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal (WIB)',
            'Kelas',
            'Mata Pelajaran',
            'NISN',
            'Nama Siswa',
            'Status',
            'Jam Scan (WIB)',
            'Keterangan',
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }
}
