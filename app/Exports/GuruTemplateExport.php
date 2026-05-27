<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuruTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['John Doe', '198001012005011001', 'Matematika, Bahasa Inggris', 'VII-A']
        ];
    }

    public function headings(): array
    {
        return [
            'NAMA LENGKAP',
            'NIP',
            'MAPEL DIAMPU',
            'KELAS DIAMPU',
        ];
    }
}
