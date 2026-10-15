<!DOCTYPE html>
<html>
<head>
    <title>Catatan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .logo { width: 70px; height: auto; display: block; margin: 0 auto 5px auto; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 5px 0; }
        .school-meta { font-size: 11px; margin: 2px 0; }
        
        .title-box { text-align: center; margin: 20px 0; }
        .title-box h3 { margin: 0; text-transform: uppercase; text-decoration: underline; }
        
        .student-info { width: 100%; margin-bottom: 20px; font-size: 12px; }
        .student-info td { padding: 3px 0; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
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
        <h3>REKAPITULASI CATATAN SISWA</h3>
    </div>

    <!-- Info Siswa -->
    <table class="student-info">
        <tr>
            <td width="15%"><strong>Nama Siswa</strong></td>
            <td width="35%">: {{ $student->name }}</td>
            <td width="15%"><strong>Kelas</strong></td>
            <td width="35%">: {{ $student->class_name }}</td>
        </tr>
        <tr>
            <td><strong>NIS / NISN</strong></td>
            <td>: {{ $student->nis }} / {{ $student->nisn }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ $student->status }}</td>
        </tr>
    </table>

    <!-- Tabel Catatan -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Jenis</th>
                <th width="25%">Judul / Kasus</th>
                <th width="40%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $record->recorded_at->format('d/m/Y') }}</td>
                    <td style="text-align: center;">
                        @if($record->type === 'achievement')
                            <span style="color: green; font-weight: bold;">PRESTASI</span>
                        @elseif($record->type === 'violation')
                            <span style="color: red; font-weight: bold;">PELANGGARAN</span>
                        @else
                            <span style="color: #d97706; font-weight: bold;">LAINNYA</span>
                        @endif
                    </td>
                    <td>{{ $record->title }}</td>
                    <td>{!! nl2br(e($record->description)) !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px;">Belum ada catatan prestasi atau pelanggaran.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>