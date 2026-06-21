<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Imports\GuruImport;
use App\Exports\GuruTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = User::where('role', 'guru');
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $mapels = \App\Models\Mapel::all();
        $kelas = \App\Models\Kelas::all();
        $gurusMapel = User::where('role', 'guru')->get(['id', 'name', 'mapel_diampu']);
        $perPage = $request->input('per_page', 10);
        $gurus = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return Inertia::render('Guru/Index', [
            'gurus' => $gurus,
            'mapels' => $mapels,
            'kelas' => $kelas,
            'gurusMapel' => $gurusMapel,
            'filters' => $request->only('search', 'per_page')
        ]);
    }

    public function downloadTemplate()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $fileName = 'template_guru.xlsx';
        \Maatwebsite\Excel\Facades\Excel::store(new GuruTemplateExport, $fileName, 'public');
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
            Excel::import(new GuruImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data guru berhasil diimport dari Excel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $fileName = 'data_guru_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\GuruExport, $fileName);
    }

    public function exportAkun(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $resetPassword = $request->boolean('reset_password', false);

        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        $akunData = [];

        foreach ($gurus as $guru) {
            $plainPassword = null;

            if ($resetPassword) {
                // Generate password baru: Guru + 4 digit angka acak
                $plainPassword = 'Guru' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $guru->password = Hash::make($plainPassword);
                $guru->save();
            }

            $akunData[] = [
                'no'       => count($akunData) + 1,
                'nama'     => $guru->name,
                'nip'      => $guru->nip ?? '-',
                'username' => $guru->username,
                'password' => $plainPassword ?? '(tidak direset)',
                'kelas'    => $guru->kelas_diampu ?? '-',
            ];
        }

        $sekolah = \App\Models\Sekolah::first();
        $tanggal = now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $resetLabel = $resetPassword ? 'Password Baru (Direset)' : 'Password';

        $html = view('exports.daftar-akun-guru', compact('akunData', 'sekolah', 'tanggal', 'resetLabel', 'resetPassword'))->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'nip' => 'nullable|string|max:255',
            'mapel_diampu' => 'nullable|array',
            'mapel_diampu.*' => 'string|max:255',
            'kelas_diampu' => 'nullable|string|max:255',
        ]);

        if (isset($validated['mapel_diampu']) && is_array($validated['mapel_diampu'])) {
            $validated['mapel_diampu'] = implode(', ', $validated['mapel_diampu']);
        }

        $validated['role'] = 'guru';
        $validated['password'] = Hash::make('Yaspih@702');

        $guru = User::create($validated);

        if ($guru->kelas_diampu) {
            \App\Models\Kelas::where('nama_kelas', $guru->kelas_diampu)->update(['wali_kelas_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan dengan password default: Yaspih@702');
    }

    public function update(Request $request, User $guru)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $guru->id,
            'nip' => 'nullable|string|max:255',
            'mapel_diampu' => 'nullable|array',
            'mapel_diampu.*' => 'string|max:255',
            'kelas_diampu' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Hapus password jika kosong (tidak diupdate)
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['mapel_diampu'])) {
            $validated['mapel_diampu'] = implode(', ', $validated['mapel_diampu']);
        }

        // Simpan kelas lama sebelum diupdate
        $oldKelas = $guru->kelas_diampu;

        $guru->update($validated);

        // Jika kelas diampu berubah, update tabel Kelas
        if ($oldKelas !== $guru->kelas_diampu) {
            // Hapus dari kelas lama
            if ($oldKelas) {
                \App\Models\Kelas::where('nama_kelas', $oldKelas)->update(['wali_kelas_id' => null]);
            }
            // Tambahkan ke kelas baru
            if ($guru->kelas_diampu) {
                \App\Models\Kelas::where('nama_kelas', $guru->kelas_diampu)->update(['wali_kelas_id' => $guru->id]);
            }
        } else if ($guru->kelas_diampu) {
             // Pastikan tabel kelas tersinkron
             \App\Models\Kelas::where('nama_kelas', $guru->kelas_diampu)->update(['wali_kelas_id' => $guru->id]);
        }

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui.' . ($request->filled('password') ? ' Password juga telah diperbarui.' : ''));
    }

    public function destroy($id)
    {
        \Illuminate\Support\Facades\Log::info("Mencoba menghapus guru dengan ID: " . $id);
        
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        if ($user->kelas_diampu) {
            \App\Models\Kelas::where('nama_kelas', $user->kelas_diampu)->update(['wali_kelas_id' => null]);
        }

        $user->delete();

        \Illuminate\Support\Facades\Log::info("Guru berhasil dihapus");
        
        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }
}
