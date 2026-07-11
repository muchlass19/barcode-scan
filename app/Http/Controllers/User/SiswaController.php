<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Siswa;
use App\Imports\User\SiswaImport;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function index()
    {
        $siswaList = Siswa::orderBy('nama_lengkap')->get();
        return view('admin.user.siswa.index', compact('siswaList'));
    }

    public function create()
    {
        return view('admin.user.siswa.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama_lengkap' => 'required',
            'password' => 'required|min:8',
        ], [
            'nisn.required' => 'NISN wajib diisi',
            'nisn.unique' => 'NISN sudah terdaftar',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        Siswa::create([
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.user.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit($nisn)
    {
        $siswa = Siswa::findOrFail($nisn);
        return view('admin.user.siswa.ubah', compact('siswa'));
    }

    public function update(Request $request, $nisn)
    {
        $siswa = Siswa::findOrFail($nisn);
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama_lengkap' => 'required',
        ], [
            'nisn.required' => 'NISN wajib diisi',
            'nisn.unique' => 'NISN sudah terdaftar',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
        ]);

        $siswa->update([
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->nama_lengkap,
        ]);

        return redirect()->route('admin.user.siswa.index')
            ->with('success', 'Siswa berhasil diubah');
    }

    public function destroy($nisn)
    {
        $siswa = Siswa::findOrFail($nisn);
        $siswa->delete();

        return redirect()->route('admin.user.siswa.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    public function changePassword($nisn)
    {
        $siswa = Siswa::findOrFail($nisn);
        return view('admin.user.siswa.ubah_password', compact('siswa'));
    }

    public function updatePassword(Request $request, $nisn)
    {
        $siswa = Siswa::findOrFail($nisn);
        $request->validate([
            'password' => 'required|min:8',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter'
        ]);

        $siswa->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.user.siswa.index')
            ->with('success', 'Siswa berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'file.required' => 'Wajib mengunggah file Excel dengan format xlsx atau xls',
            'file.mimes' => 'Format file harus berupa xlsx atau xls',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            if($import->insertedCount === 0) {
                return redirect()->back()
                    ->with('error'. 'Tidak ada data admin yang berhasil diimpor. Pastikan format kolom dan data sudah benar.');
            }

            return redirect()->route('admin.user.siswa.index')
                ->with('succes', 'Data Guru berhasil diimpor');
        } catch(\Exception $e) {
            Log::info('error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        return response()->download(public_path('templates/template-siswa.xlsx'));
    }
}
