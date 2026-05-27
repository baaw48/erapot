<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor ASTS - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm; /* Reduced margin to maximize vertical space */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt; /* Slightly smaller base font */
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        /* Kop Surat */
        .header-table {
            border-bottom: 3px double #0f172a;
            margin-bottom: 6px;
            padding-bottom: 3px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 70px;
            text-align: center;
        }
        .header-logo img {
            max-width: 60px; /* Made logo smaller as requested */
            max-height: 60px;
            height: auto;
            width: auto;
            margin-bottom: 4px; /* Added to keep logo from touching the line */
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-text h2 {
            margin: 1px 0;
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text p {
            margin: 0;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #334155;
        }
        .header-spacer {
            width: 70px;
        }
        /* Title */
        .report-title {
            text-align: center;
            font-size: 10.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 6px 0;
        }
        /* Biodata */
        .student-info {
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            font-size: 9pt;
        }
        .student-info td {
            padding: 1px 2px;
            vertical-align: top;
        }
        .student-info .label {
            width: 100px; /* Increased to prevent 'Tahun Pelajaran' from wrapping */
            color: #64748b;
            font-weight: 600;
        }
        .student-info .colon {
            width: 10px;
            color: #94a3b8;
        }
        .student-info .value {
            font-weight: bold;
            color: #0f172a;
        }
        /* Grades Table */
        .grades-table {
            margin-bottom: 6px;
            border: 1px solid #0f172a;
            font-size: 9pt;
        }
        .grades-table th {
            border: 1px solid #0f172a;
            padding: 4px 3px;
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .grades-table td {
            border: 1px solid #0f172a;
            padding: 3px 4px; /* Reduced padding */
        }
        .grades-table .center {
            text-align: center;
        }
        .grades-table .right {
            text-align: right;
            padding-right: 12px;
        }
        .grades-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .row-jumlah, .row-rata {
            background-color: #f8fafc;
        }
        /* Keterangan Colors */
        .text-success { color: #059669; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .text-indigo { color: #4338ca; font-weight: 900; }
        .text-slate { color: #64748b; font-weight: 600; }
        
        /* Grid Tables */
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 6px 0 2px 0;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 6px;
        }
        .info-grid-td {
            vertical-align: top;
            width: 48%;
        }
        .info-grid-spacer {
            width: 4%;
        }
        .side-table {
            border: 1px solid #0f172a;
            font-size: 9pt;
        }
        .side-table th, .side-table td {
            border: 1px solid #0f172a;
            padding: 2px 4px;
        }
        .side-table th {
            background-color: #f1f5f9;
        }
        .catatan-box {
            border: 1px solid #0f172a;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 6px;
            font-size: 9pt;
        }
        .catatan-text {
            font-style: italic;
            font-weight: 600;
            color: #334155;
        }
        
        /* Signature */
        .signature-table {
            margin-top: 10px;
            text-align: center;
            font-size: 9pt;
        }
        .signature-table td {
            vertical-align: bottom;
            height: 55px; /* Squeezed height to fit on page */
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-nip {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

@php
    $semuaGuru = \App\Models\User::where('role', 'guru')->get();
@endphp

@if($siswas->isEmpty())
    <div style="text-align: center; padding: 50px 20px; font-family: sans-serif;">
        <h2 style="color: #475569;">Tidak Ada Data Siswa</h2>
        <p style="color: #64748b;">Kelas {{ $kelas->nama_kelas }} belum memiliki data siswa. Silakan tambahkan siswa di menu Data Siswa terlebih dahulu sebelum mencetak rapor.</p>
    </div>
@else
@foreach($siswas as $index => $siswa)
    <div class="{{ $index < count($siswas) - 1 ? 'page-break' : '' }}">
        
        <!-- KOP SURAT -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <!-- Placeholder logo, since we may not have direct base64 image here. If user uploads, we can use it. -->
                    <!-- Currently we fall back to a text if no logo, or use empty space -->
                    @if($sekolah && $sekolah->logo_path && file_exists(public_path('storage/' . $sekolah->logo_path)))
                        <img src="{{ public_path('storage/' . $sekolah->logo_path) }}" alt="Logo">
                    @else
                        <!-- No Logo -->
                    @endif
                </td>
                <td class="header-text">
                    <h1>LAPORAN HASIL BELAJAR PESERTA DIDIK</h1>
                    <h2>{{ $sekolah ? $sekolah->nama_sekolah : 'MTs AL - HASANAH CIOMAS' }}</h2>
                    <p>TAHUN PELAJARAN {{ $tahunAktif->tahun }}</p>
                </td>
                <td class="header-spacer"></td>
            </tr>
        </table>

        <!-- TITLE -->
        <div class="report-title">
            {{ $sekolah && $sekolah->jenis_asesmen ? strtoupper('ASESMEN SUMATIF TENGAH SEMESTER (' . $sekolah->jenis_asesmen . ')') : 'ASESMEN SUMATIF TENGAH SEMESTER (ASTS)' }}
        </div>

        <!-- BIODATA -->
        <table class="student-info">
            <tr>
                <td style="width: 65%;">
                    <table>
                        <tr><td class="label">Nama Siswa</td><td class="colon">:</td><td class="value">{{ $siswa->nama_siswa }}</td></tr>
                        <tr><td class="label">NIS / NISN</td><td class="colon">:</td><td class="value">{{ $siswa->nis }} {{ $siswa->nisn ? '/ ' . $siswa->nisn : '' }}</td></tr>
                        <tr><td class="label">Kelas</td><td class="colon">:</td><td class="value">{{ $kelas->nama_kelas }}</td></tr>
                    </table>
                </td>
                <td style="width: 35%;">
                    <table>
                        <tr><td class="label">Sekolah</td><td class="colon">:</td><td class="value">{{ $sekolah ? $sekolah->nama_sekolah : 'MTs AL - HASANAH CIOMAS' }}</td></tr>
                        <tr><td class="label">Semester</td><td class="colon">:</td><td class="value">{{ $tahunAktif->semester }}</td></tr>
                        <tr><td class="label">Tahun Pelajaran</td><td class="colon">:</td><td class="value">{{ $tahunAktif->tahun }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- GRADES TABLE -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="45%">Mata Pelajaran</th>
                    <th width="10%">KKTP</th>
                    <th width="15%">Nilai</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalNilai = 0;
                    $jumlahMapel = count($mapels);
                @endphp
                @foreach($mapels as $idx => $mapel)
                    @php
                        $nilai = $siswa->nilais->firstWhere('mapel_id', $mapel->id);
                        $angka = $nilai ? $nilai->nilai_angka : null;
                        if($angka !== null) {
                            $totalNilai += $angka;
                        }
                        
                        $guruName = '';
                        foreach($semuaGuru as $g) {
                            if ($g->mapel_diampu) {
                                $gMapels = array_map('trim', explode(',', $g->mapel_diampu));
                                if (in_array($mapel->nama_mapel, $gMapels)) {
                                    $guruName = $g->name;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td class="center" style="color: #64748b; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold;">
                            {{ $mapel->nama_mapel }}
                        </td>
                        <td class="center" style="color: #475569; font-weight: bold;">{{ $kelas->kktp }}</td>
                        <td class="center" style="font-weight: 900;">{{ $angka !== null ? $angka : '-' }}</td>
                        <td class="center" style="font-weight: bold;">
                            @if($angka !== null)
                                @if($angka >= $kelas->kktp)
                                    <span class="text-success">TUNTAS</span>
                                @else
                                    <span class="text-danger">BELUM TUNTAS</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                @php
                    $rataRata = $jumlahMapel > 0 ? $totalNilai / $jumlahMapel : 0;
                @endphp
                <tr class="bg-light">
                    <td colspan="3" class="right" style="font-weight: bold;">Jumlah Nilai</td>
                    <td class="center" style="font-weight: 900;">{{ $totalNilai }}</td>
                    <td></td>
                </tr>
                <tr class="bg-light">
                    <td colspan="3" class="right" style="font-weight: bold;">Rata-rata</td>
                    <td class="center" style="font-weight: 900; color: #4338ca;">{{ number_format($rataRata, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        @php
            $catatan = $siswa->catatanWaliKelas->first();
        @endphp

        <!-- EKSKUL & KEHADIRAN GRID -->
        <table style="width: 100%; margin-bottom: 6px;">
            <tr>
                <td style="vertical-align: top; width: 42%;">
                    <div class="section-title">A. Ekstrakurikuler</div>
                    <table class="side-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="50%">Kegiatan</th>
                                <th width="20%" style="text-align: center;">Nilai</th>
                                <th width="30%" style="text-align: center;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($catatan && ($catatan->ekskul_1 || $catatan->ekskul_2 || $catatan->ekskul_3))
                                @php
                                    $ekskuls = [
                                        ['nama' => $catatan->ekskul_1, 'nilai' => $catatan->nilai_ekskul_1],
                                        ['nama' => $catatan->ekskul_2, 'nilai' => $catatan->nilai_ekskul_2],
                                        ['nama' => $catatan->ekskul_3, 'nilai' => $catatan->nilai_ekskul_3],
                                    ];
                                    $ketMap = ['A' => 'Sangat Baik', 'B' => 'Baik', 'C' => 'Cukup', 'D' => 'Kurang'];
                                @endphp
                                @foreach($ekskuls as $eks)
                                    @if($eks['nama'])
                                        <tr>
                                            <td style="font-weight: bold;">{{ $eks['nama'] }}</td>
                                            <td class="center" style="font-weight: 900; text-align: center;">{{ $eks['nilai'] }}</td>
                                            <td class="center" style="color: #334155; text-align: center;">{{ $ketMap[$eks['nilai']] ?? '-' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td class="center" style="color: #94a3b8;">-</td>
                                    <td class="center" style="color: #94a3b8;">-</td>
                                    <td class="center" style="color: #94a3b8;">-</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </td>
                <td style="width: 2%;"></td>
                <td style="vertical-align: top; width: 36%;">
                    <div class="section-title">B. Ketidakhadiran</div>
                    <table class="side-table" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td width="70%" style="color: #475569;">Sakit (S)</td>
                                <td width="30%" class="center" style="font-weight: bold;">{{ $catatan ? $catatan->sakit : 0 }} Hari</td>
                            </tr>
                            <tr>
                                <td style="color: #475569;">Izin (I)</td>
                                <td class="center" style="font-weight: bold;">{{ $catatan ? $catatan->izin : 0 }} Hari</td>
                            </tr>
                            <tr>
                                <td style="color: #475569;">Tanpa Keterangan (A)</td>
                                <td class="center" style="font-weight: bold;">{{ $catatan ? $catatan->alpa : 0 }} Hari</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 2%;"></td>
                <td style="vertical-align: top; width: 18%;">
                    <div class="section-title" style="text-align: center;">Peringkat Kelas</div>
                    <table class="side-table" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td class="center" style="vertical-align: middle; text-align: center; padding: 12px 0;">
                                    <div style="font-size: 24pt; font-weight: 900; color: #000; line-height: 1; margin-bottom: 2px;">{{ $siswa->peringkat }}</div>
                                    <div style="font-size: 8pt; color: #334155; font-weight: bold;">dari {{ count($siswas) }} Siswa</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- CATATAN WALI KELAS -->
        <div class="section-title">C. Catatan Wali Kelas</div>
        <div class="catatan-box">
            <div class="catatan-text">{{ $catatan && $catatan->catatan ? $catatan->catatan : '-' }}</div>
        </div>

        @php
            $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $tanggal = date('j') . ' ' . $bulan[date('n') - 1] . ' ' . date('Y');
            
            // Coba ambil nama kota dari alamat
            $alamat = $sekolah ? $sekolah->alamat : 'Bogor';
            $kota = 'Bogor';
            $places = ['Bogor', 'Ciomas', 'Ciapus', 'Jakarta', 'Bandung', 'Depok', 'Tangerang', 'Bekasi'];
            foreach($places as $p) {
                if (stripos($alamat, $p) !== false) {
                    $kota = $p;
                    break;
                }
            }
        @endphp

        <div style="text-align: right; font-size: 11px; font-weight: bold; color: #334155;">
            {{ $kota }}, {{ $tanggal }}
        </div>

        <!-- SIGNATURES -->
        <table class="signature-table">
            <tr>
                <td width="33%">
                    Orang Tua / Wali Siswa,<br>
                    <br><br><br><br>
                    <div style="font-size: 9pt; color: #000;">(..................................)</div>
                </td>
                <td width="34%">
                    Wali Kelas,<br>
                    <br><br><br><br>
                    <div class="signature-name">{{ $kelas->waliKelas ? $kelas->waliKelas->name : '(..................................)' }}</div>
                    @if($kelas->waliKelas && $kelas->waliKelas->nip && $kelas->waliKelas->nip !== '-')
                        <div class="signature-nip">NIP. {{ $kelas->waliKelas->nip }}</div>
                    @else
                        <div class="signature-nip">&nbsp;</div>
                    @endif
                </td>
                <td width="33%">
                    Mengetahui,<br>
                    Kepala Sekolah,<br>
                    <br><br><br><br>
                    <div class="signature-name">{{ $sekolah && $sekolah->kepala_sekolah ? $sekolah->kepala_sekolah : '(..................................)' }}</div>
                    @if($sekolah && $sekolah->nip_kepsek && $sekolah->nip_kepsek !== '-')
                        <div class="signature-nip">NIP. {{ $sekolah->nip_kepsek }}</div>
                    @else
                        <div class="signature-nip">&nbsp;</div>
                    @endif
                </td>
            </tr>
        </table>

    </div>
@endforeach
@endif

</body>
</html>
