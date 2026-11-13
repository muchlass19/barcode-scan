<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\JadwalPelajaran;
use App\Models\Pertemuan;
use App\Models\Absensi;
use App\Models\MataPelajaran;

class JadwalSiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $nisn = $siswa->nisn;

        $kelasSiswa = KelasSiswa::where('siswa_nisn', $nisn)->first();

        $jadwalSiswa = JadwalPelajaran::with(['kelas'])
            ->where('kelas_id', $kelasSiswa->kelas_id)
            ->get();

        $kelas = Kelas::find($kelasSiswa->kelas_id);
        
        session([
            'kelas' => $kelas
        ]);

        $jadwalTerformat = [];
        foreach($jadwalSiswa as $jadwal) {
            $hari = strtolower($jadwal->hari);

            $jadwalTerformat['hari'][$hari][] = [
                'jadwal_id' => $jadwal->id,
                'mata_pelajaran_id' => $jadwal->mataPelajaran->id,
                'nama_mata_pelajaran' => $jadwal->mataPelajaran->nama_mata_pelajaran,
                'jam_mulai' => $jadwal->jam_mulai_wib,
                'jam_selesai' => $jadwal->jam_selesai_wib,
                'guru' => $jadwal->guru->nama_lengkap,
                'guru_nip' => $jadwal->guru->nip
            ];
        }

        $data = [
            'nama_kelas' => $kelas->nama_kelas,
            'hari' => $jadwalTerformat['hari'] ?? []
        ];

        return view('siswa.jadwal.index', compact('data'));
    }

    public function setDetail(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        $nisn = $siswa->nisn;

        $jadwalListId = JadwalPelajaran::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->pluck('id')
            ->toArray();

        $pertemuanList = Pertemuan::whereIn('jadwal_pelajaran_id', $jadwalListId)
            ->orderBy('tanggal', 'asc')
            ->get();
        $absenSiswa = Absensi::where('siswa_nisn', $nisn)->get()->keyBy('pertemuan_id');

        $rekap = $pertemuanList->map(function ($pertemuan) use ($absenSiswa) {
            $absen = $absenSiswa->get($pertemuan->id);

            return [
                'pertemuan_id' => $pertemuan->id,
                'tanggal' => $pertemuan->tanggal_wib,
                'hadir' => $absen ? (bool) $absen->status_kehadiran : false
            ];
        });

        session([
            'rekap' => $rekap
        ]);

        return redirect()->route('siswa.jadwal.detail', $request->mata_pelajaran_id);
    }

    public function show($mataPelajaranId)
    {
        $mapel = MataPelajaran::find($mataPelajaranId);
        $rekapPertemuan = session('rekap');
        $namaKelas = session('kelas')->nama_kelas;

        return view('siswa.jadwal.detail', compact('mapel', 'rekapPertemuan', 'namaKelas'));
    }
}
