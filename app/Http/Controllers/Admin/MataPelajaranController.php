<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataPelajaranImport;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mataPelajaran = MataPelajaran::orderBy('nama_mata_pelajaran')->get();
        return view('admin.mata_pelajaran.index', compact('mataPelajaran'));
    }

    public function create()
    {
        return view('admin.mata_pelajaran.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:100|unique:mata_pelajaran,nama_mata_pelajaran',
        ], [
            'nama_mata_pelajaran.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mata_pelajaran.unique' => 'Nama mata pelajaran sudah terdaftar, silakan gunakan nama lain.'
        ]);
        MataPelajaran::create([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
        ]);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        return view('admin.mata_pelajaran.ubah', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $request->validate([
            'nama_mata_pelajaran' => 'required|string|max:100|unique:mata_pelajaran,nama_mata_pelajaran,' . $mapel->id,
        ],[
            'nama_mata_pelajaran.required' => 'Nama mata pelajaran wajib diisi.',
            'nama_mata_pelajaran.unique' => 'Nama mata pelajaran sudah terdaftar, silakan gunakan nama lain.'
        ]);

        $mapel->update([
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
        ]);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    // import excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ], [
            'file.required' => 'Wajib mengunggah file Excel dengan format xlsx atau xls',
            'file.mimes' => 'Format file harus berupa xlsx atau xls',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            Excel::import(new MataPelajaranImport, $request->file('file'));
            return redirect()->route('admin.mata-pelajaran.index')
                ->with('success', 'Data mata pelajaran berhasil diimpor');
        } catch(\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        return response()->download(public_path('templates/template-mata-pelajaran.xlsx'));
    }
}
