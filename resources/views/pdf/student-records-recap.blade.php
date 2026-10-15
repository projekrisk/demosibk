<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Catatan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .logo { width: 70px; height: auto; display: block; margin: 0 auto 5px auto; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 5px 0; }
        .school-meta { font-size: 11px; margin: 2px 0; }
        
        .title-box { text-align: center; margin: 20px 0; }
        .title-box h3 { margin: 0; text-transform: uppercase; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 15px; font-size: 11px; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .footer { margin-top: 30px; position: fixed; bottom: 0; width: 100%; font-size: 9px; text-align: right; color: #555; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>

    <!-- Footer -->
    <div class="footer">
        Dicetak otomatis oleh Sistem SIBK-digi pada: {{ now()->translatedFormat('d F Y H:i:s') }}
    </div>

    <!-- Kop Surat -->
    <div class="header">
        @if($tenant->logo_path)
            <img src="{{ public_path('uploads/' . $tenant->logo_path) }}" class="logo">
        @endif
        <div class="school-name">{{ $tenant->name }}</div>
        <div class="school-meta">NPSN: {{ $tenant->npsn }}</div>
        @if($tenant->address)
            <div class="school-meta">{{ $tenant->address }}</div>
        @endif
    </div>

    <!-- Judul -->
    <div class="title-box">
        <h3>REKAPITULASI PRESTASI & PELANGGARAN SISWA</h3>
    </div>

    <!-- Info Laporan -->
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Periode</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</td>
            <td width="15%"><strong>Filter Jenis</strong></td>
            <td width="35%">: {{ $filter_type == 'achievement' ? 'Prestasi' : ($filter_type == 'violation' ? 'Pelanggaran' : ($filter_type == 'other' ? 'Lainnya' : 'Semua')) }}</td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="15%">Jenis</th>
                <th width="38%">Judul & Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $record->recorded_at->format('d/m/Y') }}</td>
                    <td><strong>{{ $record->student->name }}</strong></td>
                    <td style="text-align: center;">{{ $record->student->class_name }}</td>
                    <td style="text-align: center;">
                        @if($record->type === 'achievement')
                            <span style="color: green; font-weight: bold;">PRESTASI</span>
                        @elseif($record->type === 'violation')
                            <span style="color: red; font-weight: bold;">PELANGGARAN</span>
                        @else
                            <span style="color: #d97706; font-weight: bold;">LAINNYA</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $record->title }}</strong><br>
                        <span style="color: #555; font-size: 11px;">{!! nl2br(e($record->description)) !!}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 15px;">Tidak ada data catatan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>