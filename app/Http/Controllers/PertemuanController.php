<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pertemuan;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PertemuanController extends Controller
{
    public function generateQr(Request $request)
    {
        $validated = $request->validate([
            'jadwal_ids' => 'required|array',
            'kelas_id'   => 'required|exists:kelas,id'
        ]);

        $tanggal = Carbon::today()->toDateString();
        $kelas_id = $validated['kelas_id'];

        // --- Tentukan hari ---
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

        // --------------------------------------------------------------------
        // Cari jadwal yang sesuai hari dari daftar jadwal_ids yang dipilih
        // --------------------------------------------------------------------
        $jadwal = JadwalPelajaran::whereIn('id', $validated['jadwal_ids'])
            ->where('hari', $hariIni)
            ->where('kelas_id', $kelas_id)
            ->first();

        if (!$jadwal) {
            return back()->with('error', 'Tidak ada jadwal untuk hari ini.');
        }

        // --------------------------------------------------------------------
        // Cek apakah pertemuan untuk jadwal & tanggal ini sudah ada
        // --------------------------------------------------------------------
        $pertemuan = Pertemuan::where('jadwal_pelajaran_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($pertemuan) {
            return redirect()
                ->route('guru.absen.qr', $pertemuan->id)
                ->with('info', 'QR Code sudah digenerate hari ini.');
        }

        // --------------------------------------------------------------------
        // Buat pertemuan baru + generate token QR
        // --------------------------------------------------------------------
        $token = Str::random(32);

        $pertemuan = Pertemuan::create([
            'jadwal_pelajaran_id' => $jadwal->id,
            'tanggal'             => $tanggal,
            'token_qr'            => $token,
            'expired_at'          => now()->addHours(1),
        ]);

        return redirect()
            ->route('guru.absen.qr', $pertemuan->id)
            ->with('success', 'QR Code berhasil digenerate.');
    }

    public function showQr($id)
    {
        $pertemuan = Pertemuan::with(['jadwalPelajaran.mataPelajaran'])
            ->findOrFail($id);
        $qrPayload = json_encode([
            'pertemuan_id' => $pertemuan->id,
            'token' => $pertemuan->token_qr
        ]);

        $qrCode = QrCode::size(300)->generate($qrPayload);

        return view('guru.absen.show_qr', compact('qrCode', 'pertemuan'));
    }

    public function setJadwal(Request $request)
    {
        $request->validate([
            'jadwal_ids' => 'required',
            'kelas_id' => 'required'
        ]);

        session([
            'jadwal_ids' => $request->jadwal_ids,
            'kelas_id' => $request->kelas_id
        ]);

        return redirect()->route('guru.pertemuan.detail');
    }

    public function show()
    {
        $jadwalIds = session('jadwal_ids');

        if(!$jadwalIds || empty($jadwalIds)) {
            return redirect()->route('guru.jadwal.index')->with('error', 'Silakan pilih jadwal terlebih dahulu');
        }

        $jadwalPertemuan = Pertemuan::whereIn('jadwal_pelajaran_id', $jadwalIds)->get();

        $infoJadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('id', $jadwalIds)
            ->first();
        
        return view('guru.pertemuan.detail', compact('jadwalPertemuan', 'infoJadwal'));
    }
}
