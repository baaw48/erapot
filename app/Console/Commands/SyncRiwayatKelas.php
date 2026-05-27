<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use Illuminate\Support\Facades\DB;

class SyncRiwayatKelas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:riwayat-kelas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi awal data kelas_id siswa ke tabel riwayat_kelas untuk tahun ajaran aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            $this->error('Tidak ada Tahun Ajaran Aktif!');
            return;
        }

        $this->info("Menyinkronkan data siswa ke Riwayat Kelas untuk Tahun Ajaran: {$tahunAjaranAktif->tahun} ({$tahunAjaranAktif->semester})");

        $siswas = Siswa::whereNotNull('kelas_id')->get();
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($siswas as $siswa) {
                RiwayatKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    ],
                    [
                        'kelas_id' => $siswa->kelas_id,
                    ]
                );
                $count++;
            }
            DB::commit();
            $this->info("Berhasil menyinkronkan {$count} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}
