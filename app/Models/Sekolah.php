<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'kepala_sekolah',
        'nip_kepsek',
        'website',
        'email',
        'logo_path',
        'npsn',
        'jenis_asesmen',
    ];

    //Accessor untuk logo_url - agar compatible dengan frontend
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return Storage::url($this->logo_path);
        }
        return null;
    }
}
