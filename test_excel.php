<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$export = new \App\Exports\SiswaTemplateExport();
$fileName = 'test_template.xlsx';
\Maatwebsite\Excel\Facades\Excel::store($export, $fileName, 'local');
$path = storage_path('app/' . $fileName);
echo "File created at: " . $path . "\n";

class TestImport implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithHeadingRow
{
    public function collection(\Illuminate\Support\Collection $rows)
    {
        echo "Rows count: " . $rows->count() . "\n";
        print_r($rows->toArray());
    }
}

\Maatwebsite\Excel\Facades\Excel::import(new TestImport, $path);
