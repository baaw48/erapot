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
            $filename = basename($this->logo_path);
            $fullPath = public_path('logos/' . $filename);
            
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath);
                $data = base64_encode(file_get_contents($fullPath));
                return 'data:' . $mime . ';base64,' . $data;
            }
        }
        return null;
    }
}
