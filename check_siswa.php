<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\Kelas::all() as $k) { 
    echo $k->id . ': ' . $k->nama_kelas . ' - Students: ' . $k->siswas()->count() . "\n"; 
}
