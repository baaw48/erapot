<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Guru - {{ $sekolah->nama_sekolah ?? 'Sekolah' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #f8fafc;
            padding: 24px;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            color: white;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-logo {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            object-fit: contain;
            background: white;
            padding: 4px;
        }

        .header-logo-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
        }

        .header-info h1 {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .header-info p {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 4px;
        }

        /* Warning badge */
        .warning-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 12px 32px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .warning-banner.info {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .warning-icon {
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Meta info */
        .meta {
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .meta-item {
            font-size: 11px;
        }

        .meta-item strong {
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 10px;
        }

        .meta-item span {
            display: block;
            margin-top: 2px;
            color: #1e293b;
            font-weight: 600;
        }

        /* Table */
        .table-container {
            padding: 24px 32px 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #1e3a5f;
            color: white;
        }

        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            white-space: nowrap;
        }

        thead th:first-child { border-radius: 6px 0 0 6px; }
        thead th:last-child  { border-radius: 0 6px 6px 0; }

        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:nth-child(even) { background: #f8fafc; }

        tbody td {
            padding: 10px 12px;
            font-size: 11px;
            color: #1e293b;
            vertical-align: middle;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-username {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-family: monospace;
            font-size: 11px;
        }

        .badge-password {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-family: monospace;
            font-size: 11px;
        }

        .badge-no-reset {
            background: #f1f5f9;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            font-style: italic;
        }

        .no-col {
            text-align: center;
            font-weight: 700;
            color: #94a3b8;
            width: 40px;
        }

        /* Footer */
        .footer {
            padding: 16px 32px;
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }

        /* Print */
        @media print {
            body {
                background: white;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                box-shadow: none;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Print Button (hidden when printing) -->
<div class="no-print" style="max-width:900px; margin:0 auto 12px; display:flex; justify-content:flex-end; gap:8px;">
    <button onclick="window.print()" style="background:#2563eb; color:white; border:none; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
        🖨️ Cetak / Print
    </button>
    <button onclick="window.close()" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; padding:10px 20px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;">
        ✕ Tutup
    </button>
</div>

<div class="page">

    <!-- Header -->
    <div class="header">
        @if($sekolah && $sekolah->getLogoUrl())
            <img src="{{ $sekolah->getLogoUrl() }}" class="header-logo" alt="Logo Sekolah">
        @else
            <div class="header-logo-placeholder">📚</div>
        @endif
        <div class="header-info">
            <h1>{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</h1>
            <p>Daftar Akun Guru &amp; Tenaga Pendidik</p>
            @if($sekolah && $sekolah->alamat)
                <p style="margin-top:2px; opacity:0.7;">{{ $sekolah->alamat }}</p>
            @endif
        </div>
    </div>

    <!-- Warning / Info Banner -->
    @if($resetPassword)
    <div class="warning-banner">
        <span class="warning-icon">⚠️</span>
        <span>RAHASIA — Password telah direset. Dokumen ini berisi password baru yang masih dapat dibaca. Harap simpan dengan aman dan serahkan langsung kepada guru yang bersangkutan.</span>
    </div>
    @else
    <div class="warning-banner info">
        <span class="warning-icon">ℹ️</span>
        <span>Password tidak ditampilkan karena tidak direset. Gunakan opsi "Reset Password" untuk mencetak password baru setiap guru.</span>
    </div>
    @endif

    <!-- Meta Info -->
    <div class="meta">
        <div class="meta-item">
            <strong>Tanggal Cetak</strong>
            <span>{{ $tanggal }}</span>
        </div>
        <div class="meta-item" style="text-align:center;">
            <strong>Total Guru</strong>
            <span>{{ count($akunData) }} orang</span>
        </div>
        <div class="meta-item" style="text-align:right;">
            <strong>Status</strong>
            <span>{{ $resetPassword ? '✅ Password Telah Direset' : 'ℹ️ Tanpa Reset Password' }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th>Nama Lengkap</th>
                    <th>NIP</th>
                    <th>Wali Kelas</th>
                    <th>Username</th>
                    <th>{{ $resetLabel }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($akunData as $akun)
                <tr>
                    <td class="no-col">{{ $akun['no'] }}</td>
                    <td style="font-weight:600;">{{ $akun['nama'] }}</td>
                    <td style="color:#64748b;">{{ $akun['nip'] }}</td>
                    <td>
                        @if($akun['kelas'] !== '-')
                            <span class="badge" style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">{{ $akun['kelas'] }}</span>
                        @else
                            <span style="color:#cbd5e1;">-</span>
                        @endif
                    </td>
                    <td><span class="badge badge-username">{{ $akun['username'] }}</span></td>
                    <td>
                        @if($resetPassword && $akun['password'] !== '(tidak direset)')
                            <span class="badge badge-password">{{ $akun['password'] }}</span>
                        @else
                            <span class="badge badge-no-reset">tidak direset</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dicetak oleh sistem E-Rapor &bull; {{ $sekolah->nama_sekolah ?? '' }} &bull; {{ $tanggal }}
    </div>

</div>
</body>
</html>
