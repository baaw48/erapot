<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\EkskulController;
use App\Models\Sekolah;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $sekolah = Sekolah::first();

    // Debug log
    \Log::info('Welcome page sekolah data: ' . json_encode($sekolah ? [
        'nama_sekolah' => $sekolah->nama_sekolah,
        'logo_url' => $sekolah->getLogoUrl(),
    ] : 'null'));

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'sekolah' => $sekolah ? [
            'id' => $sekolah->id,
            'nama_sekolah' => $sekolah->nama_sekolah,
            'logo_url' => $sekolah->getLogoUrl(),
            'alamat' => $sekolah->alamat,
        ] : null,
    ]);
});

// Debug route
Route::get('/debug-sekolah', function () {
    $sekolah = Sekolah::first();
    return response()->json([
        'exists' => $sekolah ? true : false,
        'nama_sekolah' => $sekolah ? $sekolah->nama_sekolah : 'null',
        'logo_path' => $sekolah ? $sekolah->logo_path : 'null',
        'logo_url' => $sekolah ? $sekolah->getLogoUrl() : 'null',
    ]);
});

// Route to serve logo safely without symlink issues
Route::get('/school-logo/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!\Illuminate\Support\Facades\File::exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('school.logo');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'stats' => [
            'total_kelas' => \App\Models\Kelas::count(),
            'total_siswa' => \App\Models\Siswa::where('status', 'aktif')->count(),
            'total_mapel' => \App\Models\Mapel::count(),
            'tahun_aktif' => \App\Models\TahunAjaran::where('is_active', true)->first(),
            'total_siswa_wali' => (function() {
                if (auth()->check() && auth()->user()->role === 'guru' && auth()->user()->kelas_diampu) {
                    $kelas = \App\Models\Kelas::where('nama_kelas', auth()->user()->kelas_diampu)->first();
                    if ($kelas) {
                        return \App\Models\Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->count();
                    }
                }
                return 0;
            })(),
        ],
        'chart_siswa_jk' => \App\Models\Siswa::where('status', 'aktif')->selectRaw('jenis_kelamin, count(*) as total')->groupBy('jenis_kelamin')->get(),
        'chart_siswa_kelas' => \App\Models\Kelas::withCount(['siswas' => function($q) { $q->where('status', 'aktif'); }])->get()->map(function($k) {
            return ['nama' => $k->nama_kelas, 'total' => $k->siswas_count];
        }),
        'chart_nilai_kelas' => \App\Models\Nilai::join('siswas', 'nilais.siswa_id', '=', 'siswas.id')
            ->join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->where('siswas.status', 'aktif')
            ->selectRaw('kelas.nama_kelas, avg(nilais.nilai_angka) as rata_rata')
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->get(),
        'chart_nilai_mapel' => \App\Models\Nilai::join('mapels', 'nilais.mapel_id', '=', 'mapels.id')
            ->join('siswas', 'nilais.siswa_id', '=', 'siswas.id')
            ->where('siswas.status', 'aktif')
            ->selectRaw('mapels.nama_mapel, avg(nilais.nilai_angka) as rata_rata')
            ->groupBy('mapels.id', 'mapels.nama_mapel')
            ->get(),
        'chart_kehadiran' => \App\Models\CatatanWaliKelas::join('siswas', 'catatan_wali_kelas.siswa_id', '=', 'siswas.id')
            ->where('siswas.status', 'aktif')
            ->selectRaw('sum(sakit) as sakit, sum(izin) as izin, sum(alpa) as alpa')
            ->where('tahun_ajaran_id', \App\Models\TahunAjaran::where('is_active', true)->value('id'))
            ->first(),
        'mapel_diampu' => (function() {
            if (auth()->check() && auth()->user()->role === 'guru') {
                $tahun_aktif = \App\Models\TahunAjaran::where('is_active', true)->first();
                if ($tahun_aktif) {
                    return \App\Models\Pembelajaran::with(['mapel', 'kelas'])
                        ->where('guru_id', auth()->id())
                        ->where('tahun_ajaran_id', $tahun_aktif->id)
                        ->get()
                        ->map(function ($p) use ($tahun_aktif) {
                            $total_siswa = \App\Models\Siswa::where('kelas_id', $p->kelas_id)->where('status', 'aktif')->count();
                            $siswa_dinilai = \App\Models\Nilai::where('mapel_id', $p->mapel_id)
                                ->where('tahun_ajaran_id', $tahun_aktif->id)
                                ->whereHas('siswa', function($q) use ($p) {
                                    $q->where('kelas_id', $p->kelas_id);
                                })->count();
                            
                            $status_nilai = 'Belum';
                            if ($total_siswa == 0) {
                                $status_nilai = 'Kosong';
                            } elseif ($siswa_dinilai >= $total_siswa) {
                                $status_nilai = 'Lengkap';
                            } elseif ($siswa_dinilai > 0) {
                                $status_nilai = 'Sebagian';
                            }

                            return [
                                'nama_mapel' => $p->mapel->nama_mapel ?? '-',
                                'kelas' => $p->kelas->nama_kelas ?? '-',
                                'total_siswa' => $total_siswa,
                                'siswa_dinilai' => $siswa_dinilai,
                                'status_nilai' => $status_nilai,
                            ];
                        });
                }
            }
            return [];
        })(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/logo', [SekolahController::class, 'logo'])->name('logo');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/migrate-db', function() {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migrasi database berhasil dijalankan!";
    });

    Route::get('/penilaian', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/penilaian/batch', [NilaiController::class, 'storeBatch'])->name('nilai.storeBatch');

    Route::get('/cetak-rapor', [\App\Http\Controllers\CetakRaporController::class, 'index'])->name('cetak.index');
    Route::get('/cetak-rapor/pdf', [\App\Http\Controllers\CetakRaporController::class, 'cetakPdf'])->name('cetak.pdf');
    Route::get('/cetak-leger/pdf', [\App\Http\Controllers\CetakRaporController::class, 'legerPdf'])->name('cetak-leger.pdf');

    Route::get('/arsip-rapor', [\App\Http\Controllers\CetakRaporController::class, 'arsipIndex'])->name('arsip.index');
    Route::get('/arsip-rapor/pdf', [\App\Http\Controllers\CetakRaporController::class, 'arsipPdf'])->name('arsip.pdf');
    Route::get('/arsip-leger/pdf', [\App\Http\Controllers\CetakRaporController::class, 'arsipLegerPdf'])->name('arsip-leger.pdf');

    Route::get('/pengaturan/sekolah', [\App\Http\Controllers\SekolahController::class, 'edit'])->name('sekolah.edit');
    Route::post('/pengaturan/sekolah', [\App\Http\Controllers\SekolahController::class, 'update'])->name('sekolah.update');
    Route::post('/pengaturan/rapor', [\App\Http\Controllers\SekolahController::class, 'updateRaporSetting'])->name('sekolah.raporSetting');

    Route::get('/siswa/template-import', [\App\Http\Controllers\SiswaController::class, 'downloadTemplate'])->name('siswa.template');
    Route::post('/siswa/import', [\App\Http\Controllers\SiswaController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/export', [\App\Http\Controllers\SiswaController::class, 'export'])->name('siswa.export');
    Route::resource('siswa', \App\Http\Controllers\SiswaController::class)->except(['create', 'show', 'edit']);

    Route::get('/guru/template-import', [\App\Http\Controllers\GuruController::class, 'downloadTemplate'])->name('guru.template');
    Route::post('/guru/import', [\App\Http\Controllers\GuruController::class, 'import'])->name('guru.import');
    Route::get('/guru/export', [\App\Http\Controllers\GuruController::class, 'export'])->name('guru.export');
    Route::resource('guru', \App\Http\Controllers\GuruController::class)->except(['create', 'show', 'edit']);
    Route::resource('kelas', \App\Http\Controllers\KelasController::class)->except(['create', 'show', 'edit']);
    Route::resource('mapel', \App\Http\Controllers\MapelController::class)->except(['create', 'show', 'edit']);

    Route::resource('admin', \App\Http\Controllers\AdminController::class)->except(['create', 'show', 'edit']);
    Route::resource('tahun-ajaran', \App\Http\Controllers\TahunAjaranController::class)->except(['create', 'show', 'edit']);
    Route::post('tahun-ajaran/{tahunAjaran}/set-active', [\App\Http\Controllers\TahunAjaranController::class, 'setActive'])->name('tahun-ajaran.setActive');

    Route::get('/kehadiran', [\App\Http\Controllers\KehadiranController::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran/batch', [\App\Http\Controllers\KehadiranController::class, 'storeBatch'])->name('kehadiran.storeBatch');

    Route::get('/kenaikan', [\App\Http\Controllers\KenaikanKelasController::class, 'index'])->name('kenaikan.index');
    Route::post('/kenaikan/promote', [\App\Http\Controllers\KenaikanKelasController::class, 'promote'])->name('kenaikan.promote');
    Route::post('/kenaikan/graduate', [\App\Http\Controllers\KenaikanKelasController::class, 'graduate'])->name('kenaikan.graduate');

    // Rute Pembelajaran (Jadwal Mengajar)
    Route::post('/pembelajaran/assign', [\App\Http\Controllers\PembelajaranController::class, 'assign'])->name('pembelajaran.assign');
    Route::post('/pembelajaran/assign-bulk', [\App\Http\Controllers\PembelajaranController::class, 'assignBulk'])->name('pembelajaran.assignBulk');
    Route::resource('pembelajaran', \App\Http\Controllers\PembelajaranController::class)->only(['index', 'destroy']);
});

require __DIR__.'/auth.php';
