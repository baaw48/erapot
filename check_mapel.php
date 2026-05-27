<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "MAPELS:\n";
foreach(\App\Models\Mapel::all() as $m) { 
    echo '"' . $m->nama_mapel . "\"\n"; 
}
echo "\nGURUS:\n";
foreach(\App\Models\User::where('role', 'guru')->get() as $u) { 
    echo '"' . $u->name . '": "' . $u->mapel_diampu . "\"\n"; 
}
