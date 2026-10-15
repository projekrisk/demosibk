<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perkembangan Siswa</title>
    <style>
        @page {
            size: 215mm 330mm; 
            margin: 1.5cm 2cm 1.5cm 2cm;
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000; 
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .logo { width: 70px; height: auto; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-address { font-size: 9pt; margin: 2px 0; }
        
        .report-title { 
            text-align: center; 
            margin: 10px 0 15px 0; 
            text-transform: uppercase; 
            font-weight: bold; 
            font-size: 12pt;
        }

        .info-table { width: 100%; margin-bottom: 10px; font-size: 10pt; }
        .info-table td { padding: 2px; vertical-align: top; }
        .label-col { width: 110px; font-weight: bold; }
        .separator { width: 10px; }

        .section-header {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
            background-color: #f0f0f0;
            padding: 3px 5px;
            border-left: 4px solid #333;
        }

        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
            font-size: 10pt;
        }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 4px; 
            text-align: center; 
        }
        .data-table th { background-color: #f0f0f0; }

        .content-box { 
            border: 1px solid #000; 
            padding: 5px; 
            min-height: 30px;
            margin-bottom: 5px;
            text-align: justify;
            font-size: 10pt;
        }
        .box-label { font-weight: bold; margin-bottom: 2px; display: block; }

        .footer { margin-top: 20px; width: 100%; font-size: 10pt; }
        .signature-box { 
            float: right; 
            width: 220px; 
            text-align: center; 
        }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="15%" align="center">
                @if($tenant->logo_path)
                    <img src="{{ public_path('uploads/' . $tenant->logo_path) }}" class="logo">
                @endif
            </td>
            <td width="85%" align="center">
                <div class="school-name">{{ $tenant->name }}</div>
                <div style="font-size: 10pt; font-weight: bold;">NPSN: {{ $tenant->npsn }}</div>
                @if($tenant->address)
                    <div class="school-address">{{ $tenant->address }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="report-title">
        LAPORAN PERKEMBANGAN PESERTA DIDIK <br>
        (RAPOR BIMBINGAN KONSELING)
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama Siswa</td><td class="separator">:</td>
            <td width="40%"><strong>{{ $student->name }}</strong></td>
            <td class="label-col">Tahun Ajaran</td><td class="separator">:</td>
            <td>{{ $report->academic_year }}</td>
        </tr>
        <tr>
            <td class="label-col">NIS / NISN</td><td class="separator">:</td>
            <td>{{ $student->nis }} / {{ $student->nisn }}</td>
            <td class="label-col">Semester</td><td class="separator">:</td>
            <td>{{ $report->semester }}</td>
        </tr>
        <tr>
            <td class="label-col">Kelas</td><td class="separator">:</td>
            <td>{{ $student->class_name }}</td>
            <td class="label-col"></td><td class="separator"></td>
            <td></td>
        </tr>
    </table>

    <div class="section-header">A. REKAPITULASI DATA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="33%">Prestasi</th>
                <th width="33%">Pelanggaran</th>
                <th width="33%">Layanan BK</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->total_achievements > 0 ? $report->total_achievements . ' Kali' : '-' }}</td>
                <td>{{ $report->total_violations > 0 ? $report->total_violations . ' Kali' : '-' }}</td>
                <td>{{ $report->total_counseling > 0 ? $report->total_counseling . ' Sesi' : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">B. PROFIL MASALAH (HASIL ASESMEN)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="25%">Pribadi</th>
                <th width="25%">Sosial</th>
                <th width="25%">Belajar</th>
                <th width="25%">Karir</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->assessment_pribadi }}%</td>
                <td>{{ $report->assessment_sosial }}%</td>
                <td>{{ $report->assessment_belajar }}%</td>
                <td>{{ $report->assessment_karir }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header">C. ANALISIS PERKEMBANGAN</div>
    
    <div class="content-box">
        <span class="box-label">1. Perkembangan Pribadi & Emosi</span>
        {{ $report->personality_development ?? '-' }}
    </div>

    <div class="content-box">
        <span class="box-label">2. Perkembangan Sosial</span>
        {{ $report->social_development ?? '-' }}
    </div>

    <div class="content-box">
        <span class="box-label">3. Perkembangan Belajar & Karir</span>
        {{ $report->academic_development ?? '-' }}
    </div>

    <div class="section-header">D. KESIMPULAN & REKOMENDASI</div>
    <div class="content-box" style="border: 2px solid #000; background-color: #fafafa; font-weight: 500;">
        {{ $report->general_conclusion ?? '-' }}
    </div>

    <div class="footer">
        <div class="footer">
        <div class="signature-box">
            <p>
                {{-- UPDATE: Menggunakan data dinamis dari form --}}
                {{ $signing_place ? $signing_place . ', ' : '' }} {{ \Carbon\Carbon::parse($signing_date)->translatedFormat('d F Y') }}
            </p>
            <p>Guru Bimbingan Konseling,</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $report->counselor->name ?? '(Belum Ada Konselor)' }}</p>
        </div>
        
        <div style="float: left; width: 220px; text-align: center;">
            <p>Mengetahui,</p>
            <p>Orang Tua / Wali Siswa</p>
            <br><br><br>
            <p>( ...................................... )</p>
        </div>
        
        <div class="clear"></div>
        
        <div style="text-align: center; margin-top: 15px;">
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ $tenant->headmaster_name ?? '......................................' }}</p>
            @if($tenant->headmaster_nip)
                <p>NIP. {{ $tenant->headmaster_nip }}</p>
            @endif
        </div>
    </div>

</body>
</html>