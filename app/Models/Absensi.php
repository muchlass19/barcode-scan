<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $fillable = [
        'siswa_nisn',
        'pertemuan_id',
        'tanggal',
        'waktu_scan',
        'status_kehadiran',
        'keterangan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_nisn', 'nisn');
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }
}
