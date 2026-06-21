<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['nama_lengkap'])) {
                continue;
            }

            $nip = $row['nip'] ?? '';
            $username = $nip ?: strtolower(str_replace(' ', '', $row['nama_lengkap'])) . rand(10, 99);
            
            $user = null;
            if ($nip) {
                $user = User::where('nip', $nip)->first();
            }
            if (!$user) {
                $user = User::where('username', $username)->first();
            }

            $mapel_diampu = $row['mapel_diampu'] ?? null;
            $kelas_diampu = $row['kelas_diampu'] ?? null;

            if ($user) {
                $user->update([
                    'name' => $row['nama_lengkap'],
                    'mapel_diampu' => $mapel_diampu,
                    'kelas_diampu' => $kelas_diampu,
                ]);
            } else {
                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $username,
                    'nip' => $nip,
                    'password' => Hash::make($nip ?: 'guru123'),
                    'role' => 'guru',
                    'mapel_diampu' => $mapel_diampu,
                    'kelas_diampu' => $kelas_diampu,
                ]);
            }

            if ($kelas_diampu) {
                Kelas::where('nama_kelas', $kelas_diampu)->update(['wali_kelas_id' => $user->id]);
            }
        }
    }
}
