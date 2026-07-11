<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KelasSiswaController;
use App\Http\Controllers\Admin\JadwalPelajaranController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\GuruController;
use App\Http\Controllers\User\SiswaController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\JadwalGuruController;
use App\Http\Controllers\AbsenController;
use App\Http\Controllers\PertemuanController;
use App\Http\Controllers\Siswa\JadwalSiswaController;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'adminIndex'])->name('index');
    // manajemen mata pelajaran
    Route::prefix('mata-pelajaran')->name('mata-pelajaran.')->group(function () {
        Route::get('/', [MataPelajaranController::class, 'index'])->name('index');
        Route::get('/tambah', [MataPelajaranController::class, 'create'])->name('tambah');
        Route::post('/', [MataPelajaranController::class, 'store'])->name('store');
        Route::get('/{id}/ubah', [MataPelajaranController::class, 'edit'])->name('ubah');
        Route::put('/{id}', [MataPelajaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [MataPelajaranController::class, 'destroy'])->name('hapus');

        // excel import dan download template
        Route::post('/import', [MataPelajaranController::class, 'import'])->name('import');
        Route::get('/download-template', [MataPelajaranController::class, 'download'])->name('download');
    });

    // manajemen kelas
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', [KelasController::class, 'index'])->name('index');
        Route::get('/tambah', [KelasController::class, 'create'])->name('tambah');
        Route::post('/', [KelasController::class, 'store'])->name('store');
        Route::get('/{id}/ubah', [KelasController::class, 'edit'])->name('ubah');
        Route::put('/{id}', [KelasController::class, 'update'])->name('update');
        Route::delete('/{id}', [KelasController::class, 'destroy'])->name('hapus');

        // excel import dan download template
        Route::post('/import', [KelasController::class, 'import'])->name('import');
        Route::get('/download-template', [KelasController::class, 'download'])->name('download');

        Route::prefix('detail')->name('detail.')->group(function () {
            Route::get('/download-template', [KelasSiswaController::class, 'download'])->name('download');
            Route::get('/{kelasId}', [KelasSiswaController::class, 'index'])->name('index');
            Route::get('/{kelasId}/tambah', [KelasSiswaController::class, 'create'])->name('tambah');
            Route::post('/{kelasId}', [KelasSiswaController::class, 'store'])->name('store');
            Route::delete('/{id}', [KelasSiswaController::class, 'destroy'])->name('hapus');

            // excel import dan download template
            Route::post('/{kelasId}/import', [KelasSiswaController::class, 'import'])->name('import');
        });
    });

    Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
        Route::get('/', [JadwalPelajaranController::class, 'index'])->name('index');
        Route::get('/tambah', [JadwalPelajaranController::class, 'create'])->name('tambah');
        Route::get('/kelas/{kelas_id}', [JadwalPelajaranController::class, 'show'])->name('detail');
        Route::post('/', [JadwalPelajaranController::class, 'store'])->name('store');
        Route::get('/{id}/ubah', [JadwalPelajaranController::class, 'edit'])->name('ubah');
        Route::put('/{id}', [JadwalPelajaranController::class, 'update'])->name('update');
        Route::delete('/{id}', [JadwalPelajaranController::class, 'destroy'])->name('hapus');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/tambah', [AdminController::class, 'create'])->name('tambah');
            Route::post('/', [AdminController::class, 'store'])->name('store');
            Route::get('/{nip}/ubah', [AdminController::class, 'edit'])->name('ubah');
            Route::put('/{id}', [AdminController::class, 'update'])->name('update');
            Route::get('/{nip}/ubah-password', [AdminController::class, 'changePassword'])->name('ubahPassword');
            Route::put('/{id}/update-password', [AdminController::class, 'updatePassword'])->name('updatePassword');
            Route::delete('/{nip}', [AdminController::class, 'destroy'])->name('hapus');

            Route::post('/import', [AdminController::class, 'import'])->name('import');
            Route::get('/download-template', [AdminController::class, 'download'])->name('download');
        });

        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/', [GuruController::class, 'index'])->name('index');
            Route::get('/tambah', [GuruController::class, 'create'])->name('tambah');
            Route::post('/', [GuruController::class, 'store'])->name('store');
            Route::get('/{nip}/ubah', [GuruController::class, 'edit'])->name('ubah');
            Route::put('/{id}', [GuruController::class, 'update'])->name('update');
            Route::get('/{nip}/ubah-password', [GuruController::class, 'changePassword'])->name('ubahPassword');
            Route::put('/{id}/update-password', [GuruController::class, 'updatePassword'])->name('updatePassword');
            Route::delete('/{nip}', [GuruController::class, 'destroy'])->name('hapus');

            Route::post('/import', [GuruController::class, 'import'])->name('import');
            Route::get('/download-template', [GuruController::class, 'download'])->name('download');
        });

        Route::prefix('siswa')->name('siswa.')->group(function () {
            Route::get('/', [SiswaController::class, 'index'])->name('index');
            Route::get('/tambah', [SiswaController::class, 'create'])->name('tambah');
            Route::post('/', [SiswaController::class, 'store'])->name('store');
            Route::get('/{nisn}/ubah', [SiswaController::class, 'edit'])->name('ubah');
            Route::put('/{id}', [SiswaController::class, 'update'])->name('update');
            Route::get('/{nisn}/ubah-password', [SiswaController::class, 'changePassword'])->name('ubahPassword');
            Route::put('/{id}/update-password', [SiswaController::class, 'updatePassword'])->name('updatePassword');
            Route::delete('/{nisn}', [SiswaController::class, 'destroy'])->name('hapus');

            Route::post('/import', [SiswaController::class, 'import'])->name('import');
            Route::get('/download-template', [SiswaController::class, 'download'])->name('download');
        });
    });
});

Route::middleware(['auth.guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [DashboardController::class, 'guruIndex'])->name('index');
    Route::get('/jadwal', [JadwalGuruController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/detail', [JadwalGuruController::class, 'show'])->name('jadwal.detail');
    Route::post('/absen/set', [AbsenController::class, 'setAbsenSession'])->name('absen.set');
    Route::post('/absen/manual/{siswaNisn}', [AbsenController::class, 'processAbsenManual'])->name('processManual');
    Route::get('/absen/show-qr/{pertemuanId}', [PertemuanController::class, 'showQr'])->name('absen.qr');
    Route::post('/absen/generate-qr', [PertemuanController::class, 'generateQr'])->name('absen.generate');
    Route::get('/pertemuan', [PertemuanController::class, 'show'])->name('pertemuan.detail');
    Route::post('/pertemuan/set', [PertemuanController::class, 'setJadwal'])->name('pertemuan.set');
    Route::get('/absen/{pertemuan_id}', [AbsenController::class, 'index'])->name('absen.manual');
    Route::get('/rekap-absen/export', [JadwalGuruController::class, 'export'])->name('rekap.export');
    // jadwal
});

Route::middleware(['auth.siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [DashboardController::class, 'siswaIndex'])->name('index');
    Route::get('/scan', function () {
        return view('siswa.scan');
    })->name('scan');
    Route::get('/absen/scan', [AbsenController::class, 'scan'])->name('absen.scan');

    Route::get('/jadwal', [JadwalSiswaController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/detail/{mataPelajaranId}', [JadwalSiswaController::class, 'show'])->name('jadwal.detail');
    Route::post('/jadwal/set', [JadwalSiswaController::class, 'setDetail'])->name('jadwal.setDetail');
});

Route::prefix('search')->name('search.')->group(function () {
    Route::get('/guru', [SearchController::class, 'searchGuru'])->name('guru');
    Route::get('/mapel', [SearchController::class, 'searchMapel'])->name('mapel');
    Route::get('/kelas', [SearchController::class, 'searchKelas'])->name('kelas');
    Route::get('/siswa', [SearchController::class, 'searchSiswa'])->name('siswa');
});