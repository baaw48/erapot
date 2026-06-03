<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sekolah = App\Models\Sekolah::first();
echo json_encode([
    'logo_path' => $sekolah->logo_path,
    'logo_url' => $sekolah->getLogoUrl()
]);
