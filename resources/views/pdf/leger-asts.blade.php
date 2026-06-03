<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Leger ASTS - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 6mm 8mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 7pt;
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.15;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ── KOP SURAT ── */
        .kop-table { border-bottom: 2.5px double #0f172a; margin-bottom: 4px; padding-bottom: 2px; }
        .kop-table td { vertical-align: middle; }
        .kop-logo { width: 55px; text-align: center; }
        .kop-logo img { max-width: 50px; max-height: 50px; height: auto; width: auto; }
        .kop-text { text-align: center; }
        .kop-text .inst  { font-size: 8pt; font-weight: 900; text-transform: uppercase; letter-spacing: .4px; margin: 0; }
        .kop-text .sekolah { font-size: 11pt; font-weight: 900; text-transform: uppercase; letter-spacing: .8px; margin: 1px 0; }
        .kop-text .detail { font-size: 7pt; font-weight: bold; text-transform: uppercase; color: #334155; margin: 0; }
        .kop-spacer { width: 55px; }

        /* ── JUDUL ── */
        .judul {
            text-align: center;
            font-size: 9pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin: 5px 0 3px;
        }

        /* ── INFO KELAS ── */
        .info-kelas { font-size: 8pt; font-weight: bold; margin-bottom: 4px; }
        .info-kelas td { padding: 0 2px; }

        /* ── LEDGER TABLE ── */
        .leger {
            border: 1px solid #000;
            table-layout: fixed;
            width: 100%;
        }
        .leger th, .leger td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        /* Baris header normal (No, NIS, Nama, L/P) */
        .leger th.normal {
            font-size: 6.5pt;
            font-weight: bold;
            background-color: #e2e8f0;
            padding: 2px 1px;
        }
        /* Header mapel - singkatan horizontal */
        .leger th.mapel-header {
            font-size: 6pt;
            font-weight: bold;
            background-color: #e2e8f0;
            padding: 2px 1px;
            width: 4%; /* Cukup untuk singkatan 3-4 huruf */
        }
        /* Kolom akhir (Jml, Rata, Rnk) */
        .leger th.sum-header {
            font-size: 6pt;
            font-weight: bold;
            background-color: #dbeafe;
            padding: 2px 1px;
        }
        /* Sel data */
        .leger td {
            font-size: 6.5pt;
            padding: 2px 1px;
        }
        .leger td.nama { text-align: left; font-size: 6.5pt; padding-left: 3px; }
        .leger td.nis  { font-size: 5.5pt; }
        .leger tbody tr:nth-child(even) { background-color: #f8fafc; }
        .leger .row-sum td { background-color: #eff6ff; font-weight: bold; }

        /* ── TANDA TANGAN ── */
        .ttd-table { margin-top: 10px; width: 100%; }
        .ttd-table td { text-align: center; font-size: 8pt; vertical-align: bottom; }
        .ttd-table .nama { font-weight: bold; text-decoration: underline; display: inline-block; margin-top: 42px; }
        .ttd-table .nip  { font-size: 7pt; color: #475569; }
    </style>
</head>
<body>

    @php
        function singkatanMapel($nama) {
            $custom = [
                'PENDIDIKAN AGAMA ISLAM' => 'PAI',
                'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN' => 'PPKN',
                'PENDIDIKAN PANCASILA' => 'PPKN',
                'MATEMATIKA' => 'MTK',
                'BAHASA INDONESIA' => 'B.IND',
                'BAHASA INGGRIS' => 'B.ING',
                'BAHASA SUNDA' => 'B.SND',
                'ILMU PENGETAHUAN ALAM' => 'IPA',
                'ILMU PENGETAHUAN SOSIAL' => 'IPS',
                'PENDIDIKAN JASMANI, OLAHRAGA, DAN KESEHATAN' => 'PJOK',
                'PENDIDIKAN OLAHRAGA, DAN KESEHATAN' => 'PJOK',
                'SENI BUDAYA' => 'SBD',
                'PRAKARYA / INFORMATIKA' => 'PRK',
                'PRAKARYA/INFORMATIKA' => 'PRK',
                'PRAKARYA' => 'PRK',
                'SEJARAH KEBUDAYAAN ISLAM' => 'SKI',
                'AKIDAH AKHLAK' => 'AA',
                'BACA TULIS AL-QUR\'AN' => 'BTQ',
                'BACA TULIS AL - QUR\'AN' => 'BTQ',
                'AL - QUR\'AN HADITS' => 'QH',
                'AL-QUR\'AN HADITS' => 'QH',
                'QUR\'AN HADITS' => 'QH',
                'FIKIH' => 'FKH',
                'BAHASA ARAB' => 'ARB',
            ];
            $upper = strtoupper(trim($nama));
            if (isset($custom[$upper])) {
                return $custom[$upper];
            }
            
            $words = explode(' ', $upper);
            if (count($words) > 1) {
                $singkatan = '';
                foreach ($words as $w) {
                    if (strlen($w) > 0 && !in_array($w, ['DAN', 'KATA', 'YG'])) {
                        $singkatan .= substr($w, 0, 1);
                    }
                }
                return $singkatan;
            } else {
                return substr($upper, 0, 3);
            }
        }
    @endphp

    {{-- KOP SURAT --}}
    <table class="kop-table" style="width: 100%;">
        <tr>
            <td class="kop-logo">
                @if($sekolah && $sekolah->getLogoUrl())
                    <img src="{{ $sekolah->getLogoUrl() }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                @if(!empty($sekolah->yayasan))
                    <p class="inst">{{ $sekolah->yayasan }}</p>
                @endif
                <p class="sekolah">{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</p>
                <p class="detail">
                    NPSN: {{ $sekolah->npsn ?? '-' }} 
                    @if(!empty($sekolah->nsm)) | NSM: {{ $sekolah->nsm }} @endif
                </p>
                <p class="detail">{{ $sekolah->alamat ?? '-' }}</p>
                <p class="detail">
                    @if(!empty($sekolah->telepon)) Telp: {{ $sekolah->telepon }} @endif
                    @if(!empty($sekolah->telepon) && !empty($sekolah->email)) | @endif
                    @if(!empty($sekolah->email)) Email: {{ $sekolah->email }} @endif
                </p>
            </td>
            <td class="kop-spacer"></td>
        </tr>
    </table>

    {{-- JUDUL --}}
    <div class="judul">
        LEGER NILAI {{ $sekolah && $sekolah->jenis_asesmen ? strtoupper('ASESMEN SUMATIF TENGAH SEMESTER (' . $sekolah->jenis_asesmen . ')') : 'ASESMEN SUMATIF TENGAH SEMESTER (ASTS)' }}<br>
        TAHUN PELAJARAN {{ $tahunAktif->tahun }}
    </div>

    {{-- INFO KELAS --}}
    <table class="info-kelas" style="width: 100%;">
        <tr>
            <td style="width: 8%;">Kelas</td>
            <td style="width: 1%;">:</td>
            <td style="width: 71%;">{{ $kelas->nama_kelas }}</td>
            <td style="width: 8%;">Semester</td>
            <td style="width: 1%;">:</td>
            <td style="width: 11%;">{{ $tahunAktif->semester }}</td>
        </tr>
        <tr>
            <td>Wali Kelas</td>
            <td>:</td>
            <td>{{ $kelas->waliKelas ? $kelas->waliKelas->name : '-' }}</td>
            <td>Tingkat</td>
            <td>:</td>
            <td>{{ $kelas->tingkat }}</td>
        </tr>
    </table>

    {{-- LEDGER TABLE --}}
    <table class="leger">
        <thead>
            <tr>
                {{-- Kolom tetap --}}
                <th class="normal" style="width:3%">No</th>
                <th class="normal" style="width:8%">NIS/NISN</th>
                <th class="normal text-left" style="width:14%; text-align:left; padding-left:3px;">Nama Siswa</th>
                <th class="normal" style="width:2%">L/P</th>

                {{-- Header Mapel - disingkat --}}
                @foreach($mapels as $mapel)
                    <th class="mapel-header" title="{{ $mapel->nama_mapel }}">
                        {{ singkatanMapel($mapel->nama_mapel) }}
                    </th>
                @endforeach

                {{-- Kolom ringkasan --}}
                <th class="sum-header" style="width:4%">Jml<br>Nilai</th>
                <th class="sum-header" style="width:4%">Rata-<br>rata</th>
                <th class="sum-header" style="width:3%">Rnk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="nis">{{ $siswa->nis }}{{ $siswa->nisn ? ' / ' . $siswa->nisn : '' }}</td>
                    <td class="nama">{{ $siswa->nama_siswa }}</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>

                    @foreach($mapels as $mapel)
                        @php $nilaiMapel = $siswa->nilais->firstWhere('mapel_id', $mapel->id); @endphp
                        <td>{{ $nilaiMapel ? $nilaiMapel->nilai_angka : '-' }}</td>
                    @endforeach

                    <td style="font-weight:bold; color:#1e40af;">{{ $siswa->total_nilai }}</td>
                    <td style="font-weight:bold; color:#1e40af;">{{ $siswa->rata_rata }}</td>
                    <td style="font-weight:bold; color:#1e40af;">{{ $siswa->peringkat }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    @php
        $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $tanggal = date('j') . ' ' . $bulan[date('n') - 1] . ' ' . date('Y');
        $alamat  = $sekolah ? $sekolah->alamat : '';
        $kota    = 'Bogor';
        foreach (['Bogor','Ciomas','Ciapus','Jakarta','Bandung','Depok','Tangerang','Bekasi'] as $p) {
            if (stripos($alamat, $p) !== false) { $kota = $p; break; }
        }
    @endphp

    <table class="ttd-table" style="margin-top: 6px;">
        <tr>
            <td style="width:33%">
                <br>
                Mengetahui,<br>Kepala Sekolah
                <br><br><br><br>
                <span class="nama">{{ $sekolah->kepala_sekolah ?? '____________________________' }}</span>
                @if(!empty($sekolah->nip_kepsek) && $sekolah->nip_kepsek !== '-')
                    <br><span class="nip">NIP. {{ $sekolah->nip_kepsek }}</span>
                @endif
            </td>
            <td style="width:34%"></td>
            <td style="width:33%">
                {{ $kota }}, {{ $tanggal }}<br>
                Wali Kelas {{ $kelas->nama_kelas }},
                <br><br><br><br>
                <span class="nama">{{ $kelas->waliKelas ? $kelas->waliKelas->name : '____________________________' }}</span>
                @if($kelas->waliKelas && !empty($kelas->waliKelas->nip) && $kelas->waliKelas->nip !== '-')
                    <br><span class="nip">NIP. {{ $kelas->waliKelas->nip }}</span>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
