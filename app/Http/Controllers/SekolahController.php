<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\RaporSetting;
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

        $raporSetting = RaporSetting::first();
        if (!$raporSetting) {
            $raporSetting = RaporSetting::create([]);
        }

        return Inertia::render('Pengaturan/Sekolah', [
            'sekolah' => $sekolah,
            'tahunAjarans' => $tahunAjarans,
            'raporSetting' => $raporSetting,
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
            // Save directly to public/logos to avoid symlink issues
            $file = $request->file('logo');
            $filename = $file->hashName();
            $file->move(public_path('logos'), $filename);
            $validated['logo_path'] = 'logos/' . $filename;
        }

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $sekolah->update($validated);
        } else {
            Sekolah::create($validated);
        }

        return redirect()->back()->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function updateRaporSetting(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'show_peringkat' => 'nullable|boolean',
            'show_kehadiran' => 'nullable|boolean',
            'show_ekskul' => 'nullable|boolean',
            'show_catatan' => 'nullable|boolean',
            'show_deskripsi' => 'nullable|boolean',
            'show_kepribadian' => 'nullable|boolean',
        ]);

        $raporSetting = RaporSetting::first();
        if ($raporSetting) {
            $raporSetting->update($validated);
        } else {
            RaporSetting::create($validated);
        }

        return redirect()->back()->with('success', 'Pengaturan rapor berhasil diperbarui.');
    }
}
