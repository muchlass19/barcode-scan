<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';
    protected $fillable = [
        'kelas_id',
        'guru_nip',
        'mata_pelajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai'
    ];

    /**
     * Jam mulai tanpa detik untuk ditampilkan ke user, mis. "07:00" (WIB).
     */
    public function getJamMulaiWibAttribute(): string
    {
        return $this->jam($this->jam_mulai);
    }

    /**
     * Jam selesai tanpa detik untuk ditampilkan ke user, mis. "08:00" (WIB).
     */
    public function getJamSelesaiWibAttribute(): string
    {
        return $this->jam($this->jam_selesai);
    }

    private function jam($value): string
    {
        return $value ? Carbon::parse($value)->format('H:i') : '-';
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_nip', 'nip');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id', 'id');
    }
}
