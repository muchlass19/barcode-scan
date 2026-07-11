<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\User\AdminImport;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $adminList = Admin::orderBy('nama_lengkap')->get();
        return view('admin.user.admin.index', compact('adminList'));
    }

    public function create()
    {
        return view('admin.user.admin.tambah');
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

        Admin::create([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.user.admin.index')
            ->with('success', 'Admin berhasil ditambahkan');
    }

    public function edit($nip)
    {
        $admin = Admin::findOrFail($nip);
        return view('admin.user.admin.ubah', compact('admin'));
    }

    public function update(Request $request, $nip)
    {
        $admin = Admin::findOrFail($nip);
        $request->validate([
            'nip' => 'required|unique:admin,nip',
            'nama_lengkap' => 'required'
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah terdaftar',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
        ]);

        $admin->update([
            'nip' => $request->nip,
            'nama_lengkap' => $request->nama_lengkap
        ]);

        return redirect()->route('admin.user.admin.index')
            ->with('success', 'Admin berhasil diperbarui');
    }

    public function destroy($nip)
    {
        $admin = Admin::findOrFail($nip);
        $admin->delete();

        return redirect()->route('admin.user.admin.index')
            ->with('success', 'Admin berhasil dihapus');
    }

    public function changePassword($nip)
    {
        $admin = Admin::findOrFail($nip);
        return view('admin.user.admin.ubah_password', compact('admin'));
    }

    public function updatePassword(Request $request, $nip)
    {
        $admin = Admin::findOrFail($nip);
        $request->validate([
            'password' => 'required|min:8',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter'
        ]);

        $admin->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('admin.user.admin.index')
            ->with('success', 'Password Admin berhasil diperbarui');
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
            $import = new AdminImport();
            Excel::import($import, $request->file('file'));

            if($import->insertedCount === 0) {
                return redirect()->back()
                    ->with('error'. 'Tidak ada data admin yang berhasil diimpor. Pastikan format kolom dan data sudah benar.');
            }

            return redirect()->route('admin.user.admin.index')
                ->with('succes', 'Data Admin berhasil diimpor');
        } catch(\Exception $e) {
            Log::info('error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ', $e->getMessage());
        }
    }

    public function download()
    {
        return response()->download(public_path('templates/template-admin.xlsx'));
    }
}
