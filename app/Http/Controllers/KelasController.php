<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $perPage = $request->input('per_page', 10);
        $kelas = Kelas::with('waliKelas')->orderBy('tingkat')->orderBy('nama_kelas')->paginate($perPage)->withQueryString();
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return Inertia::render('Kelas/Index', [
            'kelas' => $kelas,
            'gurus' => $gurus,
            'filters' => $request->only('per_page')
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'tingkat' => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'kktp' => 'required|integer|min:0|max:100',
        ]);

        Kelas::create($validated);

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $kelasObj = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $id,
            'tingkat' => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'kktp' => 'required|integer|min:0|max:100',
        ]);

        $kelasObj->update($validated);

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $kelasObj = Kelas::findOrFail($id);
        $kelasObj->delete();

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}
