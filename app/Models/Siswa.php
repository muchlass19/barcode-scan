<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Siswa extends Authenticatable
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'password',
    ];

    public function kelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class, 'siswa_nisn', 'nisn');
    }
}
