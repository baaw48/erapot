<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\CatatanWaliKelas;
use App\Models\Ekskul;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        
        if ($user->role === 'admin') {
            $kelas = Kelas::all();
        } else {
            // Hanya kelas yang diampu
            $kelas = Kelas::where('nama_kelas', $user->kelas_diampu)->get();
        }

        $siswas = [];
        $kelasSudahInput = [];

        $selectedKelasId = $request->input('kelas_id');

        if ($tahunAktif) {
            $kelasSudahInput = DB::table('catatan_wali_kelas')
                ->join('siswas', 'catatan_wali_kelas.siswa_id', '=', 'siswas.id')
                ->where('catatan_wali_kelas.tahun_ajaran_id', $tahunAktif->id)
                ->pluck('siswas.kelas_id')
                ->unique()
                ->values()
                ->toArray();
        }

        if ($selectedKelasId && $tahunAktif) {
            $siswas = Siswa::where(function($query) use ($selectedKelasId, $tahunAktif) {
                    // Cek di riwayat_kelas dulu
                    $query->whereHas('riwayatKelas', function ($q) use ($selectedKelasId, $tahunAktif) {
                        $q->where('kelas_id', $selectedKelasId)
                          ->where('tahun_ajaran_id', $tahunAktif->id);
                    })
                    // Fallback: jika tidak ada di riwayatKelas, cek langsung di siswa.kelas_id
                    ->orWhere('kelas_id', $selectedKelasId);
                })
                ->where('status', 'aktif')
                ->with(['catatanWaliKelas' => function ($query) use ($tahunAktif) {
                    $query->where('tahun_ajaran_id', $tahunAktif->id);
                }])
                ->orderBy('nama_siswa', 'asc')
                ->get()
                ->map(function ($siswa) {
                    $catatan = $siswa->catatanWaliKelas->first();
                    return [
                        'siswa_id' => $siswa->id,
                        'nama_siswa' => $siswa->nama_siswa,
                        'nis' => $siswa->nis,
                        'sakit' => $catatan->sakit ?? 0,
                        'izin' => $catatan->izin ?? 0,
                        'alpa' => $catatan->alpa ?? 0,
                        'ekskul_1' => $catatan->ekskul_1 ?? '',
                        'nilai_ekskul_1' => $catatan->nilai_ekskul_1 ?? '',
                        'ekskul_2' => $catatan->ekskul_2 ?? '',
                        'nilai_ekskul_2' => $catatan->nilai_ekskul_2 ?? '',
                        'ekskul_3' => $catatan->ekskul_3 ?? '',
                        'nilai_ekskul_3' => $catatan->nilai_ekskul_3 ?? '',
                        'catatan' => $catatan->catatan ?? '',
                    ];
                });
        }

        // Ambil master data ekskul
        $ekskuls = Ekskul::where('is_active', true)->orderBy('nama_ekskul')->get(['id', 'nama_ekskul']);

        return Inertia::render('Kehadiran/Index', [
            'tahunAktif' => $tahunAktif,
            'kelas' => $kelas,
            'siswas' => $siswas,
            'ekskuls' => $ekskuls,
            'kelasSudahInput' => $kelasSudahInput,
            'filters' => [
                'kelas_id' => $selectedKelasId,
            ]
        ]);
    }

    public function storeBatch(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kehadiran' => 'required|array',
            'kehadiran.*.siswa_id' => 'required|exists:siswas,id',
            'kehadiran.*.sakit' => 'nullable|integer|min:0',
            'kehadiran.*.izin' => 'nullable|integer|min:0',
            'kehadiran.*.alpa' => 'nullable|integer|min:0',
            'kehadiran.*.catatan' => 'nullable|string',
            'kehadiran.*.ekskul_1' => 'nullable|string|max:255',
            'kehadiran.*.nilai_ekskul_1' => 'nullable|string|max:10',
            'kehadiran.*.ekskul_2' => 'nullable|string|max:255',
            'kehadiran.*.nilai_ekskul_2' => 'nullable|string|max:10',
            'kehadiran.*.ekskul_3' => 'nullable|string|max:255',
            'kehadiran.*.nilai_ekskul_3' => 'nullable|string|max:10',
        ]);

        if ($user->role === 'guru') {
            $kelasTarget = Kelas::find($validated['kelas_id']);
            if ($kelasTarget->nama_kelas !== $user->kelas_diampu) {
                abort(403, 'Akses ditolak. Anda hanya bisa mengedit kehadiran untuk kelas Anda sendiri.');
            }
        }

        try {
            DB::beginTransaction();
            $tahunId = $tahunAktif->id;

            $upsertData = [];
            foreach ($validated['kehadiran'] as $item) {
                $upsertData[] = [
                    'siswa_id' => $item['siswa_id'],
                    'tahun_ajaran_id' => $tahunId,
                    'sakit' => $item['sakit'] ?? 0,
                    'izin' => $item['izin'] ?? 0,
                    'alpa' => $item['alpa'] ?? 0,
                    'ekskul_1' => $item['ekskul_1'] ?? null,
                    'nilai_ekskul_1' => $item['nilai_ekskul_1'] ?? null,
                    'ekskul_2' => $item['ekskul_2'] ?? null,
                    'nilai_ekskul_2' => $item['nilai_ekskul_2'] ?? null,
                    'ekskul_3' => $item['ekskul_3'] ?? null,
                    'nilai_ekskul_3' => $item['nilai_ekskul_3'] ?? null,
                    'catatan' => $item['catatan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($upsertData)) {
                CatatanWaliKelas::upsert(
                    $upsertData,
                    ['siswa_id', 'tahun_ajaran_id'],
                    [
                        'sakit', 'izin', 'alpa', 
                        'ekskul_1', 'nilai_ekskul_1', 
                        'ekskul_2', 'nilai_ekskul_2', 
                        'ekskul_3', 'nilai_ekskul_3', 
                        'catatan', 'updated_at'
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data kehadiran dan catatan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving batch kehadiran: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
