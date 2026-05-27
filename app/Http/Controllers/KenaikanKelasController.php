<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $siswas = [];

        $kelasAsalId = $request->query('kelas_asal_id');
        $tipe = $request->query('tipe', 'kenaikan'); // 'kenaikan' atau 'kelulusan'

        if ($kelasAsalId) {
            $tahunAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
            if ($tahunAktif) {
                $siswas = Siswa::whereHas('riwayatKelas', function($q) use ($kelasAsalId, $tahunAktif) {
                        $q->where('kelas_id', $kelasAsalId)->where('tahun_ajaran_id', $tahunAktif->id);
                    })
                    ->where('status', 'aktif')
                    ->orderBy('nama_siswa')
                    ->get();
            } else {
                $siswas = Siswa::where('kelas_id', $kelasAsalId)
                    ->where('status', 'aktif')
                    ->orderBy('nama_siswa')
                    ->get();
            }
        }

        return Inertia::render('Kenaikan/Index', [
            'kelas' => $kelas,
            'siswas' => $siswas,
            'filters' => [
                'kelas_asal_id' => $kelasAsalId,
                'tipe' => $tipe,
            ]
        ]);
    }

    public function promote(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'required|exists:kelas,id|different:kelas_asal_id',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id'
        ]);

        try {
            DB::beginTransaction();

            $tahunAktif = \App\Models\TahunAjaran::where('is_active', true)->first();

            Siswa::whereIn('id', $request->siswa_ids)
                ->where('kelas_id', $request->kelas_asal_id)
                ->where('status', 'aktif')
                ->update(['kelas_id' => $request->kelas_tujuan_id]);

            if ($tahunAktif) {
                foreach($request->siswa_ids as $siswa_id) {
                    \App\Models\RiwayatKelas::updateOrCreate(
                        ['siswa_id' => $siswa_id, 'tahun_ajaran_id' => $tahunAktif->id],
                        ['kelas_id' => $request->kelas_tujuan_id]
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' Siswa berhasil dinaikkan kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    public function graduate(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswas,id'
        ]);

        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        $tahunLulus = $tahunAjaranAktif ? $tahunAjaranAktif->tahun : date('Y');

        try {
            DB::beginTransaction();

            Siswa::whereIn('id', $request->siswa_ids)
                ->where('kelas_id', $request->kelas_asal_id)
                ->where('status', 'aktif')
                ->update([
                    'status' => 'lulus',
                    'tahun_lulus' => $tahunLulus
                ]);

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' Siswa berhasil diluluskan menjadi Alumni.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kelulusan: ' . $e->getMessage());
        }
    }
}
