<?php

namespace App\Exports;

use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapAbsenExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $nip = Auth::guard('guru')->user()->nip;

        return Absensi::with([
            'siswa',
            'pertemuan.jadwalPelajaran.kelas',
            'pertemuan.jadwalPelajaran.mataPelajaran'
        ])
        ->whereHas('pertemuan.jadwalPelajaran', function ($q) use ($nip) {
            $q->where('guru_nip', $nip);
        })
        ->get()
        ->map(function ($item, $index) {

            return [
                'No' => $index + 1,
                'Tanggal' => $item->tanggal,
                'Nama Siswa' => $item->siswa->nama_lengkap,
                'Kelas' => $item->pertemuan->jadwalPelajaran->kelas->nama_kelas,
                'Mapel' => $item->pertemuan->jadwalPelajaran->mataPelajaran->nama_mata_pelajaran,
                'Status' => ucfirst($item->status_kehadiran),
                'Jam Scan' => $item->waktu_scan,
                'Keterangan' => $item->keterangan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Status',
            'Jam Scan',
            'Keterangan',
        ];
    }
}