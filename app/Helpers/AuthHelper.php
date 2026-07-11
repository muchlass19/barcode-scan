<?php

use Illuminate\Support\Facades\Auth;

function currentUser()
{
    if (Auth::guard('admin')->check()) {
        return [
            'user' => Auth::guard('admin')->user(),
            'role' => 'Admin'
        ];
    }

    if (Auth::guard('guru')->check()) {
        return [
            'user' => Auth::guard('guru')->user(),
            'role' => 'Guru'
        ];
    }

    if (Auth::guard('siswa')->check()) {
        return [
            'user' => Auth::guard('siswa')->user(),
            'role' => 'Siswa'
        ];
    }

    return [
        'user' => null,
        'role' => null
    ];
}
