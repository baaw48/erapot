<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pembelajaran;
use App\Models\Nilai;
use App\Models\TahunAjaran;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Hanya wali kelas yang boleh mengakses
        if ($user->role !== 'guru' || !$user->kelas_diampu) {
            abort(403, 'Akses hanya untuk Wali Kelas.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        // Ambil data kelas yang diampu
        $kelas = Kelas::where('nama_kelas', $user->kelas_diampu)->first();
        $totalSiswa = $kelas
            ? Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->count()
            : 0;

        // Ambil semua pembelajaran untuk kelas ini pada tahun ajaran aktif
        $daftarMonitoring = [];

        if ($kelas && $tahunAktif) {
            $pembelajarans = Pembelajaran::with(['guru', 'mapel'])
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->get();

            foreach ($pembelajarans as $p) {
                $siswaIds = Siswa::where('kelas_id', $kelas->id)
                    ->where('status', 'aktif')
                    ->pluck('id');

                $sudahInput = Nilai::where('mapel_id', $p->mapel_id)
                    ->where('tahun_ajaran_id', $tahunAktif->id)
                    ->whereIn('siswa_id', $siswaIds)
                    ->count();

                $belumInput = $totalSiswa - $sudahInput;
                $persentase = $totalSiswa > 0 ? round(($sudahInput / $totalSiswa) * 100) : 0;

                if ($persentase >= 100) {
                    $status = 'Selesai';
                } elseif ($persentase > 0) {
                    $status = 'Sebagian';
                } else {
                    $status = 'Belum';
                }

                $daftarMonitoring[] = [
                    'mapel'        => $p->mapel->nama_mapel ?? '-',
                    'guru'         => $p->guru->name ?? '-',
                    'total_siswa'  => $totalSiswa,
                    'sudah_input'  => $sudahInput,
                    'belum_input'  => $belumInput > 0 ? $belumInput : 0,
                    'persentase'   => $persentase,
                    'status'       => $status,
                ];
            }
        }

        $totalMapel   = count($daftarMonitoring);
        $totalSelesai = count(array_filter($daftarMonitoring, fn($m) => $m['status'] === 'Selesai'));
        $totalBelum   = $totalMapel - $totalSelesai;

        return Inertia::render('Monitoring/Index', [
            'kelas'            => $kelas ? $kelas->nama_kelas : $user->kelas_diampu,
            'tahunAktif'       => $tahunAktif,
            'totalSiswa'       => $totalSiswa,
            'totalMapel'       => $totalMapel,
            'totalSelesai'     => $totalSelesai,
            'totalBelum'       => $totalBelum,
            'daftarMonitoring' => $daftarMonitoring,
        ]);
    }
}
