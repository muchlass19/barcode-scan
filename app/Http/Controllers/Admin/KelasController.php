<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Guru;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KelasImport;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function index()
    {
        $kelasList = Kelas::with('guru')->get();
        return view('admin.kelas.index', compact('kelasList'));
    }

    public function create()
    {
        $guruList = Guru::all();
        return view('admin.kelas.tambah', compact('guruList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:20',
            'guru_nip' => 'required|string|unique:kelas,guru_nip'
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi',
            'nama_kelas.max' => 'Panjang nama kelas maksimal 20 karakter',
            'guru_nip.required' => 'Wali Kelas wajib diisi',
            'guru_nip.unique' => 'Wali Kelas hanya boleh di 1 kelas'
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'guru_nip' => $request->guru_nip
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data Kelas berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('admin.kelas.ubah', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $request->validate([
            'nama_kelas' => 'required|string|max:20',
            'guru_nip' => 'required|string|unique:kelas,guru_nip'
        ], [
            'nama_kelas.required' => 'Nama Kelas wajib diisi',
            'nama_kelas.max' => 'Panjang nama kelas maksimal 20 karakter',
            'guru_nip.required' => 'Wali Kelas wajib diisi',
            'guru_nip.unique' => 'Wali Kelas hanya boleh di 1 kelas'
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'guru_nip' => $request->guru_nip
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data Kelas berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data Kelas berhasil dihapus');
    }

    // import excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ], [
            'file.required' => 'Wajib mengunggah file Excel',
            'file.mimes' => 'Format file harus berupa xlsx atau xls',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            $import = new KelasImport();
            Excel::import($import, $request->file('file'));

            if ($import->insertedCount === 0) {
                return redirect()->back()
                    ->with('error', 'Tidak ada data kelas yang berhasil diimpor. Pastikan format kolom dan data sudah benar.');
            }

            return redirect()->route('admin.kelas.index')
                ->with('success', 'Berhasil mengimpor ' . $import->insertedCount . ' data kelas.');
        } catch(\Exception $e) {
            Log::info('error: '. $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        $filePath = public_path('templates/template-kelas.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }

        return response()->download($filePath, 'template-kelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

}
