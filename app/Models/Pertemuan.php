<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pertemuan extends Model
{
    use HasFactory;
    
    protected $table = 'pertemuan';
    protected $fillable = [
        'jadwal_pelajaran_id',
        'tanggal',
        'token_qr',
        'expired_at'
    ];

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id', 'id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
