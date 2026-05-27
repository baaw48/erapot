<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $mapels = Mapel::orderBy('kelompok', 'asc')->orderBy('urutan', 'asc')->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Mapel/Index', [
            'mapels' => $mapels,
            'filters' => $request->only('per_page')
        ]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255|unique:mapels,nama_mapel',
            'kelompok' => 'nullable|string|max:50',
            'kktp' => 'nullable|integer',
            'urutan' => 'nullable|integer',
        ]);

        Mapel::create($validated);

        return redirect()->back()->with('success', 'Data mapel berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255|unique:mapels,nama_mapel,' . $id,
            'kelompok' => 'nullable|string|max:50',
            'kktp' => 'nullable|integer',
            'urutan' => 'nullable|integer',
        ]);

        $mapel->update($validated);

        return redirect()->back()->with('success', 'Data mapel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->back()->with('success', 'Data mapel berhasil dihapus.');
    }
}
