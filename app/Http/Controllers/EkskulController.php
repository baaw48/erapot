<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EkskulController extends Controller
{
    public function index()
    {
        $ekskuls = Ekskul::orderBy('nama_ekskul')->get();
        return Inertia::render('Ekskul/Index', [
            'ekskuls' => $ekskuls
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ekskul' => 'required|string|max:255|unique:ekskuls,nama_ekskul',
            'keterangan' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Ekskul::create($validated);

        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function update(Request $request, Ekskul $ekskul)
    {
        $validated = $request->validate([
            'nama_ekskul' => 'required|string|max:255|unique:ekskuls,nama_ekskul,' . $ekskul->id,
            'keterangan' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $ekskul->update($validated);

        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Ekskul $ekskul)
    {
        $ekskul->delete();
        return redirect()->back()->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}
