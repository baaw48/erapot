<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SekolahController extends Controller
{
    public function edit()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $sekolah = Sekolah::first();
        if (!$sekolah) {
            $sekolah = Sekolah::create(['nama_sekolah' => 'MTs Al-Hasanah']);
        }

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('created_at', 'desc')->get();

        return Inertia::render('Pengaturan/Sekolah', [
            'sekolah' => $sekolah,
            'tahunAjarans' => $tahunAjarans
        ]);
    }

    public function update(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepsek' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'npsn' => 'nullable|string|max:255',
            'jenis_asesmen' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->update($validated);
        } else {
            Sekolah::create($validated);
        }

        return redirect()->back()->with('success', 'Data sekolah berhasil diperbarui.');
    }
}
