<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Mapel;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakRaporController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        
        if ($user->role === 'admin') {
            $kelas = Kelas::with('waliKelas')->get();
        } else {
            // Wali Kelas
            $kelas = Kelas::with('waliKelas')->where('nama_kelas', $user->kelas_diampu)->get();
        }

        return Inertia::render('Cetak/Index', [
            'tahunAktif' => $tahunAktif,
            'kelas' => $kelas,
        ]);
    }

    public function cetakPdf(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        set_time_limit(0);
        
        $kelasId = $request->input('kelas_id');
        if (!$kelasId) {
            abort(404);
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $kelas = Kelas::with('waliKelas')->findOrFail($kelasId);
        
        if ($user->role === 'guru' && $kelas->nama_kelas !== $user->kelas_diampu) {
            abort(403, 'Akses ditolak. Anda hanya dapat mencetak rapor kelas yang Anda ampu.');
        }

        $sekolah = Sekolah::first();
        $mapels = Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->get();
        
        $siswas = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunAktif->id);
            })
            ->with(['nilais' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id)
                      ->with('mapel');
            }, 'catatanWaliKelas' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            }])
            ->get();

        // Calculate totals and ranks for all students in the class
        foreach ($siswas as $siswa) {
            $total = 0;
            foreach ($siswa->nilais as $nilai) {
                $total += $nilai->nilai_angka;
            }
            $siswa->total_nilai = $total;
        }

        // Sort: Total Nilai DESC → Alpa ASC → Izin ASC → Sakit ASC → Nama ASC
        $siswas = $siswas->sort(function ($a, $b) {
            if ($a->total_nilai != $b->total_nilai) {
                return $b->total_nilai <=> $a->total_nilai;
            }
            $catatanA = $a->catatanWaliKelas->first();
            $catatanB = $b->catatanWaliKelas->first();
            $alpaA = $catatanA ? ($catatanA->alpa ?? 0) : 0;
            $alpaB = $catatanB ? ($catatanB->alpa ?? 0) : 0;
            if ($alpaA != $alpaB) return $alpaA <=> $alpaB;
            $izinA = $catatanA ? ($catatanA->izin ?? 0) : 0;
            $izinB = $catatanB ? ($catatanB->izin ?? 0) : 0;
            if ($izinA != $izinB) return $izinA <=> $izinB;
            $sakitA = $catatanA ? ($catatanA->sakit ?? 0) : 0;
            $sakitB = $catatanB ? ($catatanB->sakit ?? 0) : 0;
            if ($sakitA != $sakitB) return $sakitA <=> $sakitB;
            return strcmp($a->nama_siswa, $b->nama_siswa);
        })->values();

        $this->assignRanks($siswas);

        // Re-sort back by Name or NIS for the loop in PDF (optional, but good practice so they print in alphabetical order)
        $siswas = $siswas->sortBy('nama_siswa')->values();

        $pdf = Pdf::loadView('pdf.rapor-asts', [
            'kelas' => $kelas,
            'tahunAktif' => $tahunAktif,
            'siswas' => $siswas,
            'sekolah' => $sekolah,
            'mapels' => $mapels,
            'logoBase64' => $sekolah ? $sekolah->getLogoUrl() : null,
        ])->setPaper([0, 0, 612.283, 936], 'portrait');

        return $pdf->stream('Rapor-ASTS-' . $kelas->nama_kelas . '.pdf');
    }

    public function legerPdf(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        set_time_limit(0);
        
        $kelasId = $request->input('kelas_id');
        if (!$kelasId) {
            abort(404);
        }

        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $kelas = Kelas::with('waliKelas')->findOrFail($kelasId);
        
        if ($user->role === 'guru' && $kelas->nama_kelas !== $user->kelas_diampu) {
            abort(403, 'Akses ditolak. Anda hanya dapat mencetak leger kelas yang Anda ampu.');
        }

        $sekolah = Sekolah::first();
        $mapels = Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->get();
        
        $siswas = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunAktif->id);
            })
            ->with(['nilais' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            }, 'catatanWaliKelas' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            }])
            ->get();

        // Calculate totals and averages
        foreach ($siswas as $siswa) {
            $total = 0;
            $count = 0;
            foreach ($siswa->nilais as $nilai) {
                $total += $nilai->nilai_angka;
                $count++;
            }
            $siswa->total_nilai = $total;
            $siswa->rata_rata = $count > 0 ? round($total / $count, 2) : 0;
        }

        // Sort: Total Nilai DESC → Alpa ASC → Izin ASC → Sakit ASC → Nama ASC
        $siswas = $siswas->sort(function ($a, $b) {
            if ($a->total_nilai != $b->total_nilai) {
                return $b->total_nilai <=> $a->total_nilai;
            }
            $catatanA = $a->catatanWaliKelas->first();
            $catatanB = $b->catatanWaliKelas->first();
            $alpaA = $catatanA ? ($catatanA->alpa ?? 0) : 0;
            $alpaB = $catatanB ? ($catatanB->alpa ?? 0) : 0;
            if ($alpaA != $alpaB) return $alpaA <=> $alpaB;
            $izinA = $catatanA ? ($catatanA->izin ?? 0) : 0;
            $izinB = $catatanB ? ($catatanB->izin ?? 0) : 0;
            if ($izinA != $izinB) return $izinA <=> $izinB;
            $sakitA = $catatanA ? ($catatanA->sakit ?? 0) : 0;
            $sakitB = $catatanB ? ($catatanB->sakit ?? 0) : 0;
            if ($sakitA != $sakitB) return $sakitA <=> $sakitB;
            return strcmp($a->nama_siswa, $b->nama_siswa);
        })->values();

        $this->assignRanks($siswas);

        // Re-sort by Nama Siswa for final display in Ledger
        $siswas = $siswas->sortBy('nama_siswa')->values();

        $pdf = Pdf::loadView('pdf.leger-asts', [
            'kelas' => $kelas,
            'tahunAktif' => $tahunAktif,
            'siswas' => $siswas,
            'sekolah' => $sekolah,
            'mapels' => $mapels,
            'logoBase64' => $sekolah ? $sekolah->getLogoUrl() : null,
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('Leger-ASTS-' . $kelas->nama_kelas . '.pdf');
    }
    public function arsipIndex(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        // Semua tahun ajaran (termasuk yang aktif) untuk dipilih
        $semuaTahun = TahunAjaran::orderByDesc('tahun')->orderByDesc('semester')->get();

        if ($user->role === 'admin') {
            $kelas = Kelas::with('waliKelas')->get();
        } else {
            $kelas = Kelas::with('waliKelas')->where('nama_kelas', $user->kelas_diampu)->get();
        }

        return Inertia::render('Arsip/Index', [
            'semuaTahun' => $semuaTahun,
            'kelas' => $kelas,
        ]);
    }

    public function arsipPdf(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        set_time_limit(0);

        $kelasId = $request->input('kelas_id');
        $tahunId = $request->input('tahun_ajaran_id');

        if (!$kelasId || !$tahunId) {
            abort(404, 'Parameter kelas dan tahun ajaran wajib diisi.');
        }

        $tahun = TahunAjaran::findOrFail($tahunId);
        $kelas = Kelas::with('waliKelas')->findOrFail($kelasId);

        if ($user->role === 'guru' && $kelas->nama_kelas !== $user->kelas_diampu) {
            abort(403, 'Akses ditolak.');
        }

        $sekolah = Sekolah::first();
        $mapels = Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->get();

        $siswas = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunId) {
                $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunId);
            })
            ->with(['nilais' => function ($query) use ($tahunId) {
                $query->where('tahun_ajaran_id', $tahunId)->with('mapel');
            }, 'catatanWaliKelas' => function ($query) use ($tahunId) {
                $query->where('tahun_ajaran_id', $tahunId);
            }])
            ->get();

        foreach ($siswas as $siswa) {
            $total = 0;
            foreach ($siswa->nilais as $nilai) {
                $total += $nilai->nilai_angka;
            }
            $siswa->total_nilai = $total;
        }

        // Sort: Total Nilai DESC → Alpa ASC → Izin ASC → Sakit ASC → Nama ASC
        $siswas = $siswas->sort(function ($a, $b) {
            if ($a->total_nilai != $b->total_nilai) {
                return $b->total_nilai <=> $a->total_nilai;
            }
            $catatanA = $a->catatanWaliKelas->first();
            $catatanB = $b->catatanWaliKelas->first();
            $alpaA = $catatanA ? ($catatanA->alpa ?? 0) : 0;
            $alpaB = $catatanB ? ($catatanB->alpa ?? 0) : 0;
            if ($alpaA != $alpaB) return $alpaA <=> $alpaB;
            $izinA = $catatanA ? ($catatanA->izin ?? 0) : 0;
            $izinB = $catatanB ? ($catatanB->izin ?? 0) : 0;
            if ($izinA != $izinB) return $izinA <=> $izinB;
            $sakitA = $catatanA ? ($catatanA->sakit ?? 0) : 0;
            $sakitB = $catatanB ? ($catatanB->sakit ?? 0) : 0;
            if ($sakitA != $sakitB) return $sakitA <=> $sakitB;
            return strcmp($a->nama_siswa, $b->nama_siswa);
        })->values();
        
        $this->assignRanks($siswas);
        $siswas = $siswas->sortBy('nama_siswa')->values();

        $pdf = Pdf::loadView('pdf.rapor-asts', [
            'kelas' => $kelas,
            'tahunAktif' => $tahun,
            'siswas' => $siswas,
            'sekolah' => $sekolah,
            'mapels' => $mapels
        ])->setPaper([0, 0, 612.283, 936], 'portrait');

        return $pdf->stream('Arsip-Rapor-' . $kelas->nama_kelas . '-' . $tahun->tahun . '.pdf');
    }

    public function arsipLegerPdf(Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'guru' && empty($user->kelas_diampu)) {
            abort(403, 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        set_time_limit(0);

        $kelasId = $request->input('kelas_id');
        $tahunId = $request->input('tahun_ajaran_id');

        if (!$kelasId || !$tahunId) {
            abort(404, 'Parameter kelas dan tahun ajaran wajib diisi.');
        }

        $tahun = TahunAjaran::findOrFail($tahunId);
        $kelas = Kelas::with('waliKelas')->findOrFail($kelasId);

        if ($user->role === 'guru' && $kelas->nama_kelas !== $user->kelas_diampu) {
            abort(403, 'Akses ditolak.');
        }

        $sekolah = Sekolah::first();
        $mapels = Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->get();

        $siswas = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunId) {
                $q->where('kelas_id', $kelasId)->where('tahun_ajaran_id', $tahunId);
            })
            ->with(['nilais' => function ($query) use ($tahunId) {
                $query->where('tahun_ajaran_id', $tahunId);
            }, 'catatanWaliKelas' => function ($query) use ($tahunId) {
                $query->where('tahun_ajaran_id', $tahunId);
            }])
            ->get();

        foreach ($siswas as $siswa) {
            $total = 0; $count = 0;
            foreach ($siswa->nilais as $nilai) {
                $total += $nilai->nilai_angka; $count++;
            }
            $siswa->total_nilai = $total;
            $siswa->rata_rata = $count > 0 ? round($total / $count, 2) : 0;
        }

        // Sort: Total Nilai DESC → Alpa ASC → Izin ASC → Sakit ASC → Nama ASC
        $siswas = $siswas->sort(function ($a, $b) {
            if ($a->total_nilai != $b->total_nilai) {
                return $b->total_nilai <=> $a->total_nilai;
            }
            $catatanA = $a->catatanWaliKelas->first();
            $catatanB = $b->catatanWaliKelas->first();
            $alpaA = $catatanA ? ($catatanA->alpa ?? 0) : 0;
            $alpaB = $catatanB ? ($catatanB->alpa ?? 0) : 0;
            if ($alpaA != $alpaB) return $alpaA <=> $alpaB;
            $izinA = $catatanA ? ($catatanA->izin ?? 0) : 0;
            $izinB = $catatanB ? ($catatanB->izin ?? 0) : 0;
            if ($izinA != $izinB) return $izinA <=> $izinB;
            $sakitA = $catatanA ? ($catatanA->sakit ?? 0) : 0;
            $sakitB = $catatanB ? ($catatanB->sakit ?? 0) : 0;
            if ($sakitA != $sakitB) return $sakitA <=> $sakitB;
            return strcmp($a->nama_siswa, $b->nama_siswa);
        })->values();
        
        $this->assignRanks($siswas);
        $siswas = $siswas->sortBy('nama_siswa')->values();

        $pdf = Pdf::loadView('pdf.leger-asts', [
            'kelas' => $kelas,
            'tahunAktif' => $tahun,
            'siswas' => $siswas,
            'sekolah' => $sekolah,
            'mapels' => $mapels
        ])->setPaper('A4', 'landscape');

        return $pdf->stream('Arsip-Leger-' . $kelas->nama_kelas . '-' . $tahun->tahun . '.pdf');
    }

    private function assignRanks($siswas)
    {
        $rank = 1;
        $actual_rank = 1;
        $prev_siswa = null;

        foreach ($siswas as $siswa) {
            if ($prev_siswa !== null) {
                $catatanSiswa = $siswa->catatanWaliKelas->first();
                $catatanPrev = $prev_siswa->catatanWaliKelas->first();
                
                $alpaSiswa = $catatanSiswa ? ($catatanSiswa->alpa ?? 0) : 0;
                $alpaPrev = $catatanPrev ? ($catatanPrev->alpa ?? 0) : 0;
                
                $izinSiswa = $catatanSiswa ? ($catatanSiswa->izin ?? 0) : 0;
                $izinPrev = $catatanPrev ? ($catatanPrev->izin ?? 0) : 0;
                
                $sakitSiswa = $catatanSiswa ? ($catatanSiswa->sakit ?? 0) : 0;
                $sakitPrev = $catatanPrev ? ($catatanPrev->sakit ?? 0) : 0;

                $is_tie = ($siswa->total_nilai == $prev_siswa->total_nilai) &&
                          ($alpaSiswa == $alpaPrev) &&
                          ($izinSiswa == $izinPrev) &&
                          ($sakitSiswa == $sakitPrev);

                if (!$is_tie) {
                    $rank = $actual_rank;
                }
            }

            $siswa->peringkat = $rank;
            $prev_siswa = $siswa;
            $actual_rank++;
        }
    }
}

