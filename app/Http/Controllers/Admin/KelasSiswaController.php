<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KelasSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Imports\KelasSiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasSiswaController extends Controller
{
    public function index($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $kelasSiswa = KelasSiswa::with(['kelas', 'siswa'])
            ->where('kelas_id', $kelasId)
            ->get();
        return view('admin.kelas_siswa.index', compact('kelasSiswa', 'kelas'));
    }

    public function create($kelasId)
    {
        $kelas = Kelas::find($kelasId);
        if(!$kelas) {
            abort(404, 'Kelas tidak ditemukan');
        }

        $siswaList = Siswa::all();
        return view('admin.kelas_siswa.tambah', compact('siswaList', 'kelas'));
    }

    public function store($kelasId, Request $request)
    {
        $request->validate([
            'siswa_nisn' => 'required',
            'tahun_ajaran' => 'required'
        ], [
            'siswa_nisn.required' => 'Siswa harus diisi',
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi'
        ]);

        KelasSiswa::create([
            'siswa_nisn' => $request->siswa_nisn,
            'kelas_id' => $kelasId,
            'tahun_ajaran' => $request->tahun_ajaran
        ]);

        return redirect()->route('admin.kelas.detail.index', $kelasId)->with('success', 'Siswa berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $kelasSiswa = KelasSiswa::findOrFail($id);
        $kelasId = $kelasSiswa->kelas_id;
        $kelasSiswa->delete();

        return redirect()->route('admin.kelas.detail.index', $kelasId)->with('success', 'Siswa berhasil dihapus');
    }

    public function import($kelasId, Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'file.required' => 'Wajib mengunggah file Excel dengan format xlsx atau xls',
            'file.mimes' => 'Format file harus berupa xlsx atau xls',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            $import = new KelasSiswaImport($kelasId);
            Excel::import($import, $request->file('file'));

            if($import->insertedCount === 0) {
                return redirect()->back()
                    ->with('error'. 'Tidak ada data siswa yang berhasil diimpor. Pastikan format kolom dan data sudah benar.');
            }

            return redirect()->route('admin.kelas.detail.index', $kelasId)
                ->with('succes', 'Data Siswa berhasil diimpor');
        } catch(\Exception $e) {
            Log::info('error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        return response()->download(public_path('templates/template-kelas-siswa.xlsx'));
    }
}
