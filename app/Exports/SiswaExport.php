<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $kelas_id;

    public function __construct($kelas_id = null)
    {
        $this->kelas_id = $kelas_id;
    }

    public function collection()
    {
        $query = Siswa::with('kelas');
        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIS',
            'NISN',
            'NAMA SISWA',
            'JENIS KELAMIN',
            'KELAS'
        ];
    }

    public function map($siswa): array
    {
        static $no = 1;
        return [
            $no++,
            $siswa->nis,
            $siswa->nisn,
            $siswa->nama_lengkap,
            $siswa->jenis_kelamin,
            $siswa->kelas ? $siswa->kelas->nama_kelas : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
