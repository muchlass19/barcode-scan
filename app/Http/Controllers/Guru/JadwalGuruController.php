<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use App\Exports\RekapAbsenExport;
use Maatwebsite\Excel\Facades\Excel;

class JadwalGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $nip = $guru->nip;

        $jadwalGuru = JadwalPelajaran::with(['kelas'])
                ->where('guru_nip', $nip)
                ->select('kelas_id')
                ->distinct()
                ->get();

        return view('guru.jadwal.index', compact('jadwalGuru', 'guru'));
    }

    public function index2()
    {
        // pengecekan hari
        $hariInggris = strtolower(Carbon::now()->format('l'));

        $hariIndonesia = [
            'monday'    => 'senin',
            'tuesday'   => 'selasa',
            'wednesday' => 'rabu',
            'thursday'  => 'kamis',
            'friday'    => 'jumat',
            'saturday'  => 'sabtu',
            'sunday'    => 'minggu',
        ];

        $hariIni = $hariIndonesia[$hariInggris];
        $tanggalSekarang = Carbon::today()->format('d-m-Y');

        $nip = Auth::guard('guru')->user()->nip;
        $jadwalGuru = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_nip', $nip)
            ->where('hari', $hariIni)
            ->get();

        return view('guru.jadwal.index', compact('jadwalGuru', 'tanggalSekarang'));
    }

    public function show()
    {
        $nip = Auth::guard('guru')->user()->nip;

        $jadwalGuru = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_nip', $nip)
            ->get()
            ->groupBy(function ($item) {
                return $item->mataPelajaran->nama_mata_pelajaran . '-' . $item->kelas->nama_kelas;
            });

        return view('guru.jadwal.detail', compact('jadwalGuru'));
    }

    public function export()
    {
        return Excel::download(new RekapAbsenExport, 'rekap-absensi.xlsx');
    }
}
