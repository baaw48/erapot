<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['123456', '0123456789', 'Ahmad Fulan', 'L', 'VII-A']
        ];
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'NAMA SISWA',
            'JENIS KELAMIN',
            'KELAS',
        ];
    }
}
