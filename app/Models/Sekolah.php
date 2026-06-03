<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getLogoUrl()
    {
        if ($this->logo_path) {
            // Use public/logos/ directly to avoid symlink issues on Windows/cPanel
            $filename = basename($this->logo_path);
            return asset('logos/' . $filename);
        }
        return null;
    }
}
