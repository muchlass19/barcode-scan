<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\KelasSiswa;
use App\Models\Absensi;
use App\Models\Pertemuan;
use Carbon\Carbon;

class AbsenController extends Controller
{
    public function index($id)
    {
        $kelas_id = session('kelas_id');
        $pertemuan_id = $id;

        if (!$kelas_id || !$pertemuan_id) {
            abort(404, 'Parameter absen tidak lengkap.');
        }

        $kelas = Kelas::findOrFail($kelas_id);
        // $mapel = MataPelajaran::findOrFail($pertemuan_id);

        // ambil seluruh siswa dalam kelas
        $kelasSiswa = KelasSiswa::with(['siswa'])
            ->join('siswa', 'kelas_siswa.siswa_nisn', '=', 'siswa.nisn')
            ->where('kelas_siswa.kelas_id', $kelas->id)
            ->orderBy('siswa.nama_lengkap', 'asc')
            ->select('kelas_siswa.*')
            ->get();

        // ambil absensi siswa untuk mapel ini
        $absensiData = Absensi::where('pertemuan_id', $pertemuan_id)
            ->get()
            ->keyBy('siswa_nisn');

        // array status kehadiran
        $statusKehadiran = [];

        foreach ($kelasSiswa as $ks) {
            $nisn = $ks->siswa->nisn;

            if (isset($absensiData[$nisn])) {
                $statusKehadiran[$nisn] = $absensiData[$nisn]->status_kehadiran;
            } else {
                $statusKehadiran[$nisn] = 0; // default: belum absen
            }
        }

        return view('guru.absen.index', compact('kelasSiswa', 'statusKehadiran', 'pertemuan_id'));
    }

    public function setAbsenSession(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'pertemuan_id' => 'required'
        ]);

        session([
            'kelas_id' => $request->kelas_id,
            'pertemuan_id' => $request->pertemuan_id
        ]);

        return redirect()->route('guru.absen.manual');
    }

    public function processAbsenManual($siswaNisn, Request $request)
    {
        $request->validate([
            'is_absen' => 'required|in:0,1',
            'pertemuan_id' => 'required|exists:pertemuan,id'
        ]);

        $pertemuanId = $request->pertemuan_id;

        // tanggal hari ini
        $tanggal = Carbon::today()->toDateString();

        // Waktu scan WIB
        $waktuScan = Carbon::now()->addHours(7);

        $isAbsen = $request->is_absen;

        // Cek apakah data absensi sudah ada
        $absensi = Absensi::where('siswa_nisn', $siswaNisn)
            ->where('pertemuan_id', $pertemuanId)
            ->where('tanggal', $tanggal)
            ->first();

        if ($absensi) {
            // update jika sudah ada
            $absensi->update([
                'waktu_scan' => $waktuScan,
                'status_kehadiran' => $isAbsen,
                'keterangan' => 'Absen manual'
            ]);
        } else {
            // buat data jika belum ada
            Absensi::create([
                'siswa_nisn' => $siswaNisn,
                'pertemuan_id' => $pertemuanId,
                'tanggal' => $tanggal,
                'waktu_scan' => $waktuScan,
                'status_kehadiran' => $isAbsen,
                'keterangan' => 'Absen manual'
            ]);
        }

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function scan(Request $request)
    {
        // dd($request->pertemuanId);
        $pertemuanId = $request->pertemuan_id;
        $token = $request->token;

        $tanggal = Carbon::today()->toDateString();
        $waktuScan = Carbon::now()->toTimeString();

        if(!$pertemuanId || !$token) {
            return back()->with('error', 'QR Tidak valid');
        }

        $pertemuan = Pertemuan::where('id', $pertemuanId)
            ->where('token_qr', $token)
            ->first();

        if(!$pertemuan) {
            return back()->with('error', 'QR Code tidak cocok');
        }

        if(Carbon::now()->greaterThan($pertemuan->expired_at)) {
            return back()->with('error', 'QR Code sudah expired');
        }

        $siswa = Auth::guard('siswa')->user();

        // dd($waktuScan);

        $sudahAbsen = Absensi::where('siswa_nisn', $siswa->nisn)
            ->where('pertemuan_id', $pertemuanId)
            ->exists();

        if($sudahAbsen) {
            return back()->with('info', 'Kamu sudah absen');
        }

        Absensi::create([
            'siswa_nisn' => $siswa->nisn,
            'pertemuan_id' => $pertemuanId,
            'tanggal' => $tanggal,
            'waktu_scan' => $waktuScan,
            'status_kehadiran' => 1,
            'keterangan' => 'Absen QR'
        ]);

        return redirect()->route('siswa.index')->with('success', 'Berhasil absen');
    }
}
