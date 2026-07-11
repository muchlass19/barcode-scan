<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Imports\User\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    public function index()
    {
        $guruList = Guru::orderBy('nama_lengkap')->get();
        return view('admin.user.guru.index', compact('guruList'));
    }

    public function create()
    {
        return view('admin.user.guru.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:admin,nip',
            'nama_lengkap' => 'required',
            'password' => 'required|min:8'
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter'
        ]);

        Guru::create([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.user.guru.index')
            ->with('success', 'Guru berhasil ditambahkan');
    }

    public function edit($nip)
    {
        $guru = Guru::findOrFail($nip);
        return view('admin.user.guru.ubah', compact('guru'));
    }

    public function update(Request $request, $nip)
    {
        $guru = Guru::findOrFail($nip);
        $request->validate([
            'nip' => 'required|unique:guru,nip',
            'nama_lengkap' => 'required'
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
        ]);

        $guru->update([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap
        ]);

        return redirect()->route('admin.user.guru.index')
            ->with('success', 'Guru berhasil diubah');
    }

    public function destroy($nip)
    {
        $guru = Guru::findOrFail($nip);
        $guru->delete();

        return redirect()->route('admin.user.guru.index')
            ->with('success', 'Guru berhasil dihapus');
    }

    public function changePassword($nip)
    {
        $guru = Guru::findOrFail($nip);
        return view('admin.user.guru.ubah_password', compact('guru'));
    }

    public function updatePassword(Request $request, $nip)
    {
        $guru = Guru::findOrFail($nip);
        $request->validate([
            'password' => 'required|min:8',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter'
        ]);

        $guru->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.user.guru.index')
            ->with('success', 'Password Guru berhasil diperbarui');
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
            $import = new GuruImport();
            Excel::import($import, $request->file('file'));

            if($import->insertedCount === 0) {
                return redirect()->back()
                    ->with('error'. 'Tidak ada data admin yang berhasil diimpor. Pastikan format kolom dan data sudah benar.');
            }

            return redirect()->route('admin.user.guru.index')
                ->with('succes', 'Data Guru berhasil diimpor');
        } catch(\Exception $e) {
            Log::info('error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        return response()->download(public_path('templates/template-guru.xlsx'));
    }
}
