<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;

class SearchController extends Controller
{
    public function searchGuru(Request $request)
    {
        $search = $request->get('q', '');

        $gurus = Guru::query()
            ->where('nama_lengkap', 'like', "%{$search}%")
            ->orWhere('nip', 'like', "{$search}")
            ->limit(10)
            ->get(['nip', 'nama_lengkap']);

        return response()->json($gurus);
    }

    public function searchMapel(Request $request)
    {
        $search = $request->get('q', '');

        $mapels = MataPelajaran::query()
            ->where('nama_mata_pelajaran', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nama_mata_pelajaran']);

        return response()->json($mapels);
    }

    public function searchKelas(Request $request)
    {
        $search = $request->get('q', '');

        $kelas = Kelas::query()
            ->where('nama_kelas', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nama_kelas']);

        return response()->json($kelas);
    }

    public function searchSiswa(Request $request)
    {
        $search = $request->get('q', '');

        $siswa = Siswa::query()
            ->where('nama_lengkap', 'like', "%{$search}%")
            ->whereDoesntHave('kelasSiswa')
            ->limit(10)
            ->get(['nisn', 'nama_lengkap']);

        if ($siswa->isEmpty()) {
            return response()->json([
                'empty' => true,
                'message' => 'Seluruh siswa telah memiliki kelas'
            ]);
        }

        return response()->json($siswa);
    }
}

