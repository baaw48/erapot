<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = User::where('role', 'admin');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $admins = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Index', [
            'admins' => $admins,
            'filters' => $request->only('search', 'per_page')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return redirect()->back()->with('success', 'Data admin berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $admin = User::findOrFail($id);

        if ($admin->role !== 'admin') {
            abort(403, 'Hanya dapat mengedit pengguna dengan akses admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($admin->id)],
            'password' => ['nullable', Password::defaults()],
        ]);

        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function exportAkun(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $resetPassword = $request->boolean('reset_password', false);

        $admins = User::where('role', 'admin')->orderBy('name')->get();

        $akunData = [];

        foreach ($admins as $admin) {
            $plainPassword = null;

            // Jangan reset password akun sendiri (admin yg sedang login)
            if ($resetPassword && $admin->id !== auth()->id()) {
                // Generate password baru: Admin + 4 digit angka acak
                $plainPassword = 'Admin' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $admin->password = Hash::make($plainPassword);
                $admin->save();
            }

            $akunData[] = [
                'no'       => count($akunData) + 1,
                'nama'     => $admin->name,
                'username' => $admin->username,
                'password' => $plainPassword ?? ($admin->id === auth()->id() && $resetPassword ? '(akun sendiri)' : '(disembunyikan)'),
            ];
        }

        $sekolah = \App\Models\Sekolah::first();
        $tanggal = now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $resetLabel = $resetPassword ? 'Password Baru (Direset)' : 'Password';

        $html = view('exports.daftar-akun-admin', compact('akunData', 'sekolah', 'tanggal', 'resetLabel', 'resetPassword'))->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $admin = User::findOrFail($id);

        // Prevent self deletion
        if ($admin->id === auth()->user()->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        if ($admin->role !== 'admin') {
            abort(403, 'Hanya dapat menghapus pengguna dengan akses admin.');
        }

        $admin->delete();

        return redirect()->back()->with('success', 'Data admin berhasil dihapus.');
    }
}
