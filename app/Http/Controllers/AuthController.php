<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        // cek admin
        $admin = Admin::where('nip', $username)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.index');
        }

        // cek guru
        $guru = Guru::where('nip', $username)->first();
        if ($guru && Hash::check($password, $guru->password)) {
            Auth::guard('guru')->login($guru);
            return redirect()->route('guru.index');
        }

        // cek siswa
        $siswa = Siswa::where('nisn', $username)->first();
        if ($siswa && Hash::check($password, $siswa->password)) {
            Auth::guard('siswa')->login($siswa);
            return redirect()->route('siswa.index');
        }

        return back()->with('error', 'NIP/NISN atau password salah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

}
