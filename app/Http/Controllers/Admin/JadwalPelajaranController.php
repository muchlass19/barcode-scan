<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;

class JadwalPelajaranController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::all();
        return view('admin.jadwal_pelajaran.index', compact('kelasList'));
    }

    public function create()
    {
        $guruList = Guru::all();
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();

        return view('admin.jadwal_pelajaran.tambah', compact('guruList', 'kelasList', 'mataPelajaranList'));
    }

    public function show($kelasId)
    {
        $kelas = Kelas::find($kelasId);

        if (!$kelas) {
            abort(404, "Kelas tidak ditemukan");
        }

        $jadwalKelas = JadwalPelajaran::with(['kelas', 'guru', 'mataPelajaran'])
            ->where('kelas_id', $kelasId)
            ->get();

        $namaKelas = $kelas->nama_kelas;
        $idKelas = $kelas->id;

        if($jadwalKelas->isEmpty()) {
            $data = [
                'kelas_id' => $idKelas,
                'nama_kelas' => $namaKelas,
                'hari' => []
            ];
            return view('admin.jadwal_pelajaran.detail', [
                'data' => $data,
                'message' => 'Belum ada jadwal untuk kelas ini!'
            ]);
        }

        $jadwalTerformat = [];

        foreach($jadwalKelas as $jadwal) {
            $hari = strtolower($jadwal->hari);

            $jadwalTerformat['hari'][$hari][] = [
                'jadwal_id' => $jadwal->id,
                'mata_pelajaran_id' => $jadwal->mataPelajaran->id,
                'nama_mata_pelajaran' => $jadwal->mataPelajaran->nama_mata_pelajaran,
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'guru' => $jadwal->guru->nama_lengkap,
                'guru_nip' => $jadwal->guru->nip
            ];
        }

        $data = [
            'kelas_id' => $idKelas,
            'nama_kelas' => $namaKelas,
            'hari' => $jadwalTerformat['hari'] ?? []
        ];

        return view('admin.jadwal_pelajaran.detail', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'guru_nip' => 'required|string',
            'mata_pelajaran_id' => 'required',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        JadwalPelajaran::create([
            'kelas_id' => $request->kelas_id,
            'guru_nip' => $request->guru_nip,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai
        ]);

        return redirect()->route('admin.jadwal-pelajaran.detail', $request->kelas_id)->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit($id)
    {
        dd($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_nip' => 'required|string',
            'mata_pelajaran_id' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->update([
            'guru_nip' => $request->guru_nip,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai
        ]);

        return redirect()->route('admin.jadwal-pelajaran.detail', $jadwal->kelas_id)
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $kelasId = $jadwal->kelas_id;
        $jadwal->delete();

        return redirect()->route('admin.jadwal-pelajaran.detail', $kelasId)
            ->with('success', 'Jadwal berhasil dihapus');
    }
}
