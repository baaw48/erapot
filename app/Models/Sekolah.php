<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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

    // Append logo_url to all responses
    protected $appends = ['logo_url'];

    //Accessor untuk logo_url
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            // Check if file exists in storage
            $fullPath = storage_path('app/public/' . $this->logo_path);
            if (file_exists($fullPath)) {
                return asset('storage/' . $this->logo_path);
            }
        }
        return null;
    }
}
