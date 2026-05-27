<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Mapel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun dummy admin & guru
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $guru = User::create([
            'name' => 'Guru Mapel',
            'username' => 'guru',
            'role' => 'guru',
            'password' => Hash::make('password'),
        ]);

        // 2. Data Master Tahun Ajaran
        $tahun = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => true,
        ]);

        // 3. Data Master Kelas
        $kelas = Kelas::create([
            'nama_kelas' => '7-A',
            'tingkat' => 7,
            'wali_kelas_id' => $guru->id,
        ]);

        // 4. Data Master Mapel
        $mapels = [
            ['nama_mapel' => 'Matematika', 'kelompok' => 'A'],
            ['nama_mapel' => 'Bahasa Indonesia', 'kelompok' => 'A'],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam', 'kelompok' => 'A'],
        ];

        foreach ($mapels as $m) {
            Mapel::create($m);
        }

        // 5. Data Master Siswa
        for ($i = 1; $i <= 5; $i++) {
            Siswa::create([
                'nis' => '2025000' . $i,
                'nisn' => '009000000' . $i,
                'nama_siswa' => 'Siswa Dummy ' . $i,
                'kelas_id' => $kelas->id,
            ]);
        }
    }
}
