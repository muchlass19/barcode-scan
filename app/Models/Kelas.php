<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = [
        'nama_kelas',
        'guru_nip'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_nip', 'nip');
    }
}
