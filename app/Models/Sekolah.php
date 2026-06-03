<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    /**
     * Get all possible paths to check for logo file
     */
    protected function getLogoSearchPaths(): array
    {
        $paths = [];

        // If logo_path exists, try various path combinations
        if ($this->logo_path) {
            $paths[] = public_path($this->logo_path);
            $paths[] = public_path('storage/' . $this->logo_path);
            $paths[] = storage_path('app/public/' . $this->logo_path);

            // Also check with logos/ prefix if not present
            $filename = basename($this->logo_path);
            $paths[] = public_path('logos/' . $filename);
            $paths[] = public_path('storage/logos/' . $filename);
            $paths[] = base_path('public/logos/' . $filename);
            $paths[] = base_path('logos/' . $filename);
        }

        // Always check default logo locations
        $paths[] = public_path('logos/logo.png');
        $paths[] = public_path('logos/logo.jpg');
        $paths[] = public_path('logos/logo.jpeg');
        $paths[] = public_path('storage/logos/logo.png');
        $paths[] = public_path('img/logo.png');
        $paths[] = public_path('images/logo.png');

        return array_unique($paths);
    }

    public function getLogoPath(): ?string
    {
        foreach ($this->getLogoSearchPaths() as $path) {
            if ($path && file_exists($path) && is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    public function getLogoUrl(): ?string
    {
        $path = $this->getLogoPath();

        if (!$path) {
            Log::debug('Logo tidak ditemukan untuk sekolah ID: ' . $this->id . ', logo_path: ' . ($this->logo_path ?? 'null'));
            return null;
        }

        if (!file_exists($path) || !is_readable($path)) {
            Log::warning('Logo file exists but not readable: ' . $path);
            return null;
        }

        try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);

            if ($data === false) {
                Log::warning('Tidak bisa membaca file logo: ' . $path);
                return null;
            }

            $mimeTypes = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
            ];

            $mime = $mimeTypes[strtolower($type)] ?? 'image/png';
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        } catch (\Exception $e) {
            Log::error('Error generating logo URL: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if logo exists
     */
    public function hasLogo(): bool
    {
        return $this->getLogoPath() !== null;
    }
}
