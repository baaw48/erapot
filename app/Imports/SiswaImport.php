<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Cache kelas untuk mempercepat import
        $kelasCache = Kelas::pluck('id', 'nama_kelas')->toArray();

        foreach ($rows as $row) {
            if (!isset($row['nama_siswa']) || !isset($row['nis'])) {
                $headers = json_encode(array_keys($row->toArray()));
                throw new \Exception("Gagal: Kolom Nama Siswa atau NIS tidak terdeteksi di Excel. Kolom yang terbaca sistem adalah: {$headers}. Pastikan tidak mengubah baris judul (header) dari template asli.");
            }

            $nis = $row['nis'];
            $nama_kelas = $row['kelas'] ?? null;
            
            $kelas_id = null;
            if ($nama_kelas && isset($kelasCache[$nama_kelas])) {
                $kelas_id = $kelasCache[$nama_kelas];
            } else if ($nama_kelas) {
                // Coba cari case-insensitive
                $kelas = Kelas::where('nama_kelas', $nama_kelas)->first();
                if ($kelas) {
                    $kelas_id = $kelas->id;
                    $kelasCache[$nama_kelas] = $kelas_id;
                }
            }
            
            if (!$kelas_id) {
                throw new \Exception("Gagal: Kelas '{$nama_kelas}' tidak ditemukan untuk siswa {$row['nama_siswa']}. Pastikan penulisan Kelas di Excel SAMA PERSIS dengan di sistem (contoh: VII-A).");
            }

            $siswa = Siswa::where('nis', $nis)->first();
            if ($siswa) {
                $siswa->update([
                    'nama_siswa' => $row['nama_siswa'],
                    'nisn' => $row['nisn'] ?? null,
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                    'kelas_id' => $kelas_id,
                ]);
            } else {
                $siswa = Siswa::create([
                    'nis' => $nis,
                    'nama_siswa' => $row['nama_siswa'],
                    'nisn' => $row['nisn'] ?? null,
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                    'kelas_id' => $kelas_id,
                ]);
            }

            // Sync ke RiwayatKelas untuk tahun ajaran aktif
            $tahunAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
            if ($tahunAktif) {
                \App\Models\RiwayatKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAktif->id,
                    ],
                    [
                        'kelas_id' => $kelas_id,
                    ]
                );
            }
        }
    }
}
