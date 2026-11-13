<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * Jam tersimpan sebagai waktu WIB (GMT+7) mengikuti config('app.timezone'),
     * jadi expired_at selalu dibaca sebagai Carbon zona Asia/Jakarta.
     */
    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Tanggal untuk ditampilkan ke user, mis. "15 Januari 2026" (WIB).
     */
    public function getTanggalWibAttribute(): string
    {
        return $this->tanggal
            ? Carbon::parse($this->tanggal)->translatedFormat('d F Y')
            : '-';
    }

    /**
     * Jam kadaluarsa QR untuk ditampilkan ke user, mis. "15 Januari 2026, 08:00 WIB".
     */
    public function getExpiredAtWibAttribute(): string
    {
        return $this->expired_at
            ? Carbon::parse($this->expired_at)->translatedFormat('d F Y, H:i') . ' WIB'
            : '-';
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id', 'id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
