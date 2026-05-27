<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanWaliKelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id', 'tahun_ajaran_id', 'sakit', 'izin', 'alpa',
        'ekskul_1', 'nilai_ekskul_1', 'ekskul_2', 'nilai_ekskul_2',
        'ekskul_3', 'nilai_ekskul_3', 'catatan'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
