<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        $selectedKelasId = $request->input('kelas_id');
        $selectedMapelId = $request->input('mapel_id');

        $user = auth()->user();
        if ($user->role === 'guru') {
            if (!$tahunAktif) {
                $mapels = collect([]);
                $kelas = collect([]);
            } else {
                $pembelajarans = \App\Models\Pembelajaran::where('guru_id', $user->id)
                    ->where('tahun_ajaran_id', $tahunAktif->id)
                    ->with(['mapel', 'kelas'])
                    ->get();
                
                $mapels = $pembelajarans->pluck('mapel')->unique('id')->values();
                
                if ($selectedMapelId) {
                    $kelas = $pembelajarans->where('mapel_id', $selectedMapelId)->pluck('kelas')->unique('id')->values();
                } else {
                    $kelas = $pembelajarans->pluck('kelas')->unique('id')->values();
                }
            }
        } else {
            $mapels = Mapel::all();
            $kelas = Kelas::all();
        }
        $siswas = [];
        $mapelSudahInput = [];

        if ($selectedKelasId && $tahunAktif) {
            $mapelSudahInput = DB::table('nilais')
                ->join('riwayat_kelas', function ($join) use ($tahunAktif, $selectedKelasId) {
                    $join->on('nilais.siswa_id', '=', 'riwayat_kelas.siswa_id')
                         ->where('riwayat_kelas.tahun_ajaran_id', '=', $tahunAktif->id)
                         ->where('riwayat_kelas.kelas_id', '=', $selectedKelasId);
                })
                ->where('nilais.tahun_ajaran_id', $tahunAktif->id)
                ->pluck('nilais.mapel_id')
                ->unique()
                ->values()
                ->toArray();
        }

        if ($selectedKelasId && $selectedMapelId && $tahunAktif) {
            // Ambil siswa beserta nilainya untuk mapel dan tahun ajaran tsb
            // Prioritas: riwayatKelas > siswa.kelas_id langsung
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
                ->with(['nilais' => function ($query) use ($selectedMapelId, $tahunAktif) {
                    $query->where('mapel_id', $selectedMapelId)
                          ->where('tahun_ajaran_id', $tahunAktif->id);
                }])
                ->get()
                ->map(function ($siswa) {
                    return [
                        'siswa_id' => $siswa->id,
                        'nama_siswa' => $siswa->nama_siswa,
                        'nis' => $siswa->nis,
                        'nilai_angka' => $siswa->nilais->first()->nilai_angka ?? null,
                    ];
                });
        }

        return Inertia::render('Penilaian/Index', [
            'tahunAktif' => $tahunAktif,
            'kelas' => $kelas,
            'mapels' => $mapels,
            'siswas' => $siswas,
            'mapelSudahInput' => $mapelSudahInput,
            'filters' => [
                'kelas_id' => $selectedKelasId,
                'mapel_id' => $selectedMapelId,
            ]
        ]);
    }

    public function storeBatch(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        
        $validated = $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nilais' => 'required|array',
            'nilais.*.siswa_id' => 'required|exists:siswas,id',
            'nilais.*.nilai_angka' => 'nullable|integer|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $mapelId = $validated['mapel_id'];
            $tahunId = $tahunAktif->id;

            $upsertData = [];
            foreach ($validated['nilais'] as $item) {
                if ($item['nilai_angka'] !== null && $item['nilai_angka'] !== '') {
                    $upsertData[] = [
                        'siswa_id' => $item['siswa_id'],
                        'mapel_id' => $mapelId,
                        'tahun_ajaran_id' => $tahunId,
                        'nilai_angka' => $item['nilai_angka'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($upsertData)) {
                Nilai::upsert(
                    $upsertData,
                    ['siswa_id', 'mapel_id', 'tahun_ajaran_id'],
                    ['nilai_angka', 'updated_at']
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Nilai berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving batch nilai: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan nilai.');
        }
    }

}
