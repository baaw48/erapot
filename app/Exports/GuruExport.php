<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return User::where('role', 'guru')->orderBy('name', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA GURU',
            'NIP',
            'USERNAME',
            'MAPEL DIAMPU',
            'KELAS DIAMPU'
        ];
    }

    public function map($guru): array
    {
        static $no = 1;
        return [
            $no++,
            $guru->name,
            $guru->nip,
            $guru->username,
            $guru->mapel_diampu,
            $guru->kelas_diampu
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
