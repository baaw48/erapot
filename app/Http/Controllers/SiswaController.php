<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $query = Siswa::with('kelas')->where('status', 'aktif');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $siswas = $query->orderBy('created_at', 'asc')->paginate($perPage)->withQueryString();

        return Inertia::render('Siswa/Index', [
            'siswas' => $siswas,
            'kelas' => $kelas,
            'filters' => $request->only('kelas_id', 'search', 'per_page')
        ]);
    }

    public function downloadTemplate()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $fileName = 'template_siswa.xlsx';
        \Maatwebsite\Excel\Facades\Excel::store(new SiswaTemplateExport, $fileName, 'public');
        return response()->download(storage_path('app/public/' . $fileName), $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data siswa berhasil diimport dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $kelas_id = $request->query('kelas_id');
        $fileName = 'data_siswa_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new \App\Exports\SiswaExport($kelas_id), $fileName);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:50|unique:siswas,nis',
            'nisn' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        Siswa::create($validated);

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:50|unique:siswas,nis,' . $siswa->id,
            'nisn' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $siswa->update($validated);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }
}
