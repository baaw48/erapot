<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TahunAjaranController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware(function ($request, $next) {
                if (auth()->user()->role !== 'admin') {
                    abort(403, 'Akses ditolak.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('created_at', 'desc')->get();
        return \Inertia\Inertia::render('TahunAjaran/Index', [
            'tahunAjarans' => $tahunAjarans
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|string|in:Ganjil,Genap',
        ]);

        // Cek apakah sudah ada dengan tahun dan semester yang sama
        $exists = \App\Models\TahunAjaran::where('tahun', $validated['tahun'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tahun pelajaran dengan semester tersebut sudah ada.');
        }

        // Jika ini adalah tahun ajaran pertama yang dibuat, jadikan aktif secara otomatis
        $count = \App\Models\TahunAjaran::count();
        $validated['is_active'] = $count === 0;

        \App\Models\TahunAjaran::create($validated);

        return redirect()->back()->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|string|in:Ganjil,Genap',
        ]);

        $tahunAjaran->update($validated);

        return redirect()->back()->with('success', 'Data tahun pelajaran berhasil diubah.');
    }

    public function destroy(\App\Models\TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus tahun pelajaran yang sedang aktif.');
        }

        // Cek apakah ada data nilai yang terikat dengan tahun ajaran ini
        if ($tahunAjaran->nilais()->count() > 0 || \App\Models\CatatanWaliKelas::where('tahun_ajaran_id', $tahunAjaran->id)->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus tahun pelajaran karena sudah berisi data nilai atau kehadiran siswa.');
        }

        $tahunAjaran->delete();

        return redirect()->back()->with('success', 'Tahun pelajaran berhasil dihapus.');
    }

    public function setActive(\App\Models\TahunAjaran $tahunAjaran)
    {
        // Set all to inactive
        \App\Models\TahunAjaran::where('id', '>', 0)->update(['is_active' => false]);
        
        // Set selected to active
        $tahunAjaran->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Tahun pelajaran berhasil diaktifkan. Seluruh sistem sekarang menggunakan tahun pelajaran ini.');
    }
}
