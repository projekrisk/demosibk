<!DOCTYPE html>
<html>
<head>
    <title>Jurnal Kegiatan BK</title>
    <style>
        @page {
            margin: 2cm 2cm 3cm 2cm;
        }

        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .logo { width: 70px; height: auto; display: block; margin: 0 auto 5px auto; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 5px 0; }
        .school-meta { font-size: 11px; margin: 2px 0; }
        
        .title-box { text-align: center; margin: 20px 0; }
        .title-box h3 { margin: 0; text-transform: uppercase; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .footer { margin-top: 30px; page-break-inside: avoid; }
        .signature-box { float: right; width: 200px; text-align: center; }

        .footer-fixed {
            position: fixed;
            bottom: -2cm;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 9px;
            font-style: italic;
            text-align: right;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="footer-fixed">
        Dicetak otomatis oleh Sistem SIBK-digi pada: {{ now()->translatedFormat('d F Y H:i:s') }}
    </div>

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

    <div class="title-box">
        <h3>JURNAL KEGIATAN HARIAN GURU BK</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama Guru</strong></td>
            <td width="35%">: {{ $user->name }}</td>
            <td width="15%"><strong>Periode</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Hari, Tanggal</th>
                <th width="60%">Uraian Kegiatan / Aktivitas</th>
                <th width="15%">Durasi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMinutes = 0; @endphp
            @forelse($journals as $index => $journal)
                @php $totalMinutes += $journal->duration_minutes; @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        {{ $journal->date->translatedFormat('l, d F Y') }}
                    </td>
                    <td>
                        {!! nl2br(e($journal->activity)) !!}
                    </td>
                    <td style="text-align: center;">
                        {{ $journal->duration_minutes }} Menit
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 15px;">Belum ada catatan jurnal pada periode ini.</td>
                </tr>
            @endforelse
            
            @if($journals->count() > 0)
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 10px;">Total Jam Kerja Efektif</td>
                    <td style="text-align: center;">{{ round($totalMinutes / 60, 1) }} Jam</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>
                @php
                    $city = '..................';
                    if($tenant->address) {
                        $parts = explode(',', $tenant->address);
                        $city = trim(end($parts)); 
                    }
                @endphp
                {{ $city }}, {{ now()->translatedFormat('d F Y') }}
            </p>
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $tenant->headmaster_name ?? '......................................' }}</p>
            <p>NIP. {{ $tenant->headmaster_nip ?? '...................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>