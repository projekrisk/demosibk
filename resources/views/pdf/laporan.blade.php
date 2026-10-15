<!DOCTYPE html>
<html>
<head>
    <title>Laporan Layanan BK</title>
    <style>
        @page {
            size: landscape;
            margin: 2cm 2cm 3cm 2cm;
        }
        
        body { font-family: sans-serif; font-size: 12px; }
        
        .header { 
            text-align: left; 
            margin-bottom: 20px; 
            border-bottom: 3px double #000; 
            padding-bottom: 15px; 
            position: relative;
            min-height: 80px;
        }
        
        .logo { 
            width: 60px; 
            height: auto; 
            position: absolute;
            left: 0;
            top: 0;
        }

        .school-info {
            padding-left: 90px; 
            padding-right: 90px;
        }

        .school-name { 
            font-size: 18px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 0;
            line-height: 1.2;
        }
        .school-npsn { font-size: 14px; font-weight: bold; margin: 2px 0; }
        .school-address { font-size: 11px; margin: 0; }
        
        .report-title { text-align: center; margin: 20px 0; text-transform: uppercase; text-decoration: underline; font-weight: bold; }
        .report-info { margin-bottom: 15px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        table.data-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid; 
            width: 100%;
            line-height: 1;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            line-height: 1;
        }

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
            <img src="{{ public_path('uploads/' . $tenant->logo_path) }}" class="logo" alt="Logo">
        @endif
        
        <div class="school-info">
            <h1 class="school-name">{{ $tenant->name }}</h1>
            <p class="school-npsn">NPSN: {{ $tenant->npsn }}</p>
            @if($tenant->address)
                <p class="school-address">{{ $tenant->address }}</p>
            @endif
        </div>
    </div>

    <div class="report-title">
        LAPORAN PELAKSANAAN LAYANAN BIMBINGAN KONSELING
    </div>

    <div class="report-info">
        <table style="border: none; width: auto;">
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0;"><strong>Periode Laporan</strong></td>
                <td style="border: none; padding: 2px;">: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 10px 2px 0;"><strong>Filter Layanan</strong></td>
                <td style="border: none; padding: 2px;">: {{ $filterType }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="10%">TA/Sem</th> 
                <th width="18%">Sasaran</th>
                <th width="15%">Jenis Layanan</th>
                <th width="25%">Topik / Masalah</th>
                <th width="15%">Konselor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $index => $service)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $service->occurred_at->translatedFormat('d F Y') }}<br><small>{{ $service->occurred_at->format('H:i') }}</small></td>
                <td style="text-align: center; font-size: 11px;">
                    {{ $service->academic_year ?? '-' }}<br>
                    {{ $service->semester ?? '-' }}
                </td>
                <td>
                    @if($service->service_type == 'Bimbingan Klasikal')
                        Kelas {{ $service->target_class }}
                    @elseif(in_array($service->service_type, ['Bimbingan Kelompok', 'Konseling Kelompok']))
                        {{ $service->students->count() }} Siswa (Kelompok)
                    @else
                        {{ $service->student->name ?? '-' }}
                    @endif
                </td>
                <td>{{ $service->service_type }}</td>
                <td>
                    <strong>{{ $service->topic }}</strong><br>
                    <small>Status: 
                        @if($service->status == 'completed') Selesai 
                        @elseif($service->status == 'pending') Menunggu
                        @elseif($service->status == 'ongoing') Proses
                        @else {{ ucfirst($service->status) }} @endif
                    </small>
                </td>
                <td>{{ $service->counselor->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data layanan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p>Koordinator BK / Kepala Sekolah</p>
            <br><br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $tenant->headmaster_name ?? '( ...................................... )' }}</p>
            @if($tenant->headmaster_nip)
                <p>NIP. {{ $tenant->headmaster_nip }}</p>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>