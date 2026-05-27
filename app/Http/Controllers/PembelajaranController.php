<?php

namespace App\Http\Controllers;

use App\Models\Pembelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PembelajaranController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        $totalMapel = Mapel::count();

        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get(['id', 'nama_kelas', 'tingkat']);
        
        if ($tahunAktif) {
            $kelasCounts = Pembelajaran::select('kelas_id', \Illuminate\Support\Facades\DB::raw('count(DISTINCT mapel_id) as total'))
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->groupBy('kelas_id')
                ->pluck('total', 'kelas_id');
                
            $kelas->transform(function($k) use ($kelasCounts, $totalMapel) {
                $count = $kelasCounts[$k->id] ?? 0;
                if ($count == 0) {
                    $k->status_color = 'red';
                    $k->status_text = 'Kosong';
                } elseif ($count < $totalMapel) {
                    $k->status_color = 'yellow';
                    $k->status_text = 'Kurang ' . ($totalMapel - $count) . ' Mapel';
                } else {
                    $k->status_color = 'green';
                    $k->status_text = 'Lengkap';
                }
                return $k;
            });
        }

        $selectedKelasId = $request->input('kelas_id', $kelas->first()->id ?? null);

        $pembelajarans = collect();
        if ($tahunAktif && $selectedKelasId) {
            $pembelajarans = Pembelajaran::with('guru')
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where('kelas_id', $selectedKelasId)
                ->get()
                ->keyBy('mapel_id');
        }

        return Inertia::render('Pembelajaran/Index', [
            'pembelajarans' => $pembelajarans, // Mapped by mapel_id
            'gurus' => User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'nip']),
            'kelas' => $kelas,
            'mapels' => Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->get(['id', 'nama_mapel', 'kelompok']),
            'tahunAktif' => $tahunAktif,
            'selectedKelasId' => $selectedKelasId
        ]);
    }

    public function assign(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran aktif!');
        }

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'guru_id' => 'nullable|exists:users,id',
        ]);

        if (empty($validated['guru_id'])) {
            // Delete if no guru is selected (Kosongkan)
            Pembelajaran::where('kelas_id', $validated['kelas_id'])
                ->where('mapel_id', $validated['mapel_id'])
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->delete();
            
            return redirect()->back()->with('success', 'Penugasan guru berhasil dikosongkan.');
        } else {
            // Update or Create
            Pembelajaran::updateOrCreate(
                [
                    'kelas_id' => $validated['kelas_id'],
                    'mapel_id' => $validated['mapel_id'],
                    'tahun_ajaran_id' => $tahunAktif->id,
                ],
                [
                    'guru_id' => $validated['guru_id']
                ]
            );

            return redirect()->back()->with('success', 'Penugasan guru berhasil disimpan.');
        }
    }

    public function assignBulk(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran aktif!');
        }

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'assignments' => 'required|array',
            'assignments.*.mapel_id' => 'required|exists:mapels,id',
            'assignments.*.guru_id' => 'nullable|exists:users,id',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($validated['assignments'] as $assignment) {
                if (empty($assignment['guru_id'])) {
                    // Kosongkan
                    Pembelajaran::where('kelas_id', $validated['kelas_id'])
                        ->where('mapel_id', $assignment['mapel_id'])
                        ->where('tahun_ajaran_id', $tahunAktif->id)
                        ->delete();
                } else {
                    // Update or Create
                    Pembelajaran::updateOrCreate(
                        [
                            'kelas_id' => $validated['kelas_id'],
                            'mapel_id' => $assignment['mapel_id'],
                            'tahun_ajaran_id' => $tahunAktif->id,
                        ],
                        [
                            'guru_id' => $assignment['guru_id']
                        ]
                    );
                }
            }
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Semua penugasan kelas berhasil disimpan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pembelajaran = Pembelajaran::findOrFail($id);
        $pembelajaran->delete();

        return redirect()->back()->with('success', 'Data penugasan berhasil dihapus.');
    }
}
