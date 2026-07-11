<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Absensi;
use App\Models\KelasSiswa;
use App\Models\Pertemuan;

class DashboardController extends Controller
{
    public function adminIndex()
    {
        $admin = Admin::count();
        $kelas = Kelas::count();
        $siswa = Siswa::count();
        $guru = Guru::count();

        $data = [
            'admin' => $admin,
            'kelas' => $kelas,
            'siswa' => $siswa,
            'guru' => $guru
        ];
        return view('admin.index', compact('data'));
    }

    public function guruIndex()
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
        $sekarang = Carbon::now()->format('H:i');

        // pengecekan jadwal terdekat
        $nip = Auth::guard('guru')->user()->nip;
        $jadwalTerdekat = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_nip', $nip)
            ->where('hari', $hariIni)
            ->where('jam_selesai', '<=', $sekarang)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('guru.index', compact('jadwalTerdekat'));
    }

    public function siswaIndex()
    {
        // persentase absen semua mata pelajaran
        // jumlah absen / jumlah pertemuan semua mapel * 100
        $nisn = Auth::guard('siswa')->user()->nisn;
        $totalAbsen = Absensi::where('siswa_nisn', $nisn)->count();
        $kelasSiswa = KelasSiswa::where('siswa_nisn', $nisn)->first();
        $jadwalPelajaranIds = JadwalPelajaran::where('kelas_id', $kelasSiswa->kelas_id)->pluck('id')->toArray();
        $totalPertemuan = Pertemuan::whereIn('jadwal_pelajaran_id', $jadwalPelajaranIds)->count();

        $persentaseKehadiran = ($totalAbsen / $totalPertemuan) * 100;
        $persentaseAbsen = 100 - $persentaseKehadiran;

        return view('siswa.index', compact('persentaseKehadiran', 'persentaseAbsen'));
    }
}
