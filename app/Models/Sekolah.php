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

    public function getLogoPath()
    {
        if (!$this->logo_path) return null;

        $filename = basename($this->logo_path);
        
        $pathsToCheck = [
            public_path($this->logo_path), 
            public_path('storage/' . $this->logo_path),
            storage_path('app/public/' . $this->logo_path),
            public_path('logos/' . $filename),
            public_path('storage/logos/' . $filename)
        ];

        foreach ($pathsToCheck as $path) {
            if (file_exists($path) && is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    public function getLogoUrl()
    {
        $path = $this->getLogoPath();
        if ($path) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}
