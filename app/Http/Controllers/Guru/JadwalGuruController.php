<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use App\Exports\RekapAbsenExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Pertemuan;

class JadwalGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $nip = $guru->nip;

        $jadwalGuru = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->where('guru_nip', $nip)
            ->get()
            ->groupBy(function ($item) {
                return $item->mataPelajaran->nama_mata_pelajaran . '-' . $item->kelas->nama_kelas;
            });

        return view('guru.jadwal.index', compact('jadwalGuru', 'guru'));
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
        $nip = Auth::guard('guru')->user()->nip;

        $adaPertemuan = Pertemuan::whereHas('jadwalPelajaran', function ($q) use ($nip) {
            $q->where('guru_nip', $nip);
        })->exists();

        if (!$adaPertemuan) {
            return redirect()->back()->with('error', 'Belum ada pertemuan yang bisa direkap. Tidak ada data yang bisa diekspor');
        }

        return Excel::download(new RekapAbsenExport($nip), 'rekap-absensi.xlsx');
    }
}
