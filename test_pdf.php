<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rapor-asts', [
        'kelas' => \App\Models\Kelas::find(2), 
        'tahunAktif' => \App\Models\TahunAjaran::first(), 
        'siswas' => \App\Models\Siswa::where('kelas_id', 2)->get(), 
        'sekolah' => \App\Models\Sekolah::first(), 
        'mapels' => \App\Models\Mapel::get()
    ]); 
    $output = $pdf->output(); 
    echo "SUCCESS, PDF length: " . strlen($output); 
} catch(\Exception $e) { 
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString(); 
}
