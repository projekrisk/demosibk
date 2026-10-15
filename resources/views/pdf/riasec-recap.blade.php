<!DOCTYPE html>
<html>
<head>
    <title>Rekap Hasil Penjurusan RIASEC</title>
    <style>
        @page { size: 330mm 215mm; margin: 1.5cm; } /* Landscape F4 */
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; }
        
        .header { position: relative; text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; min-height: 80px; font-family: Arial, sans-serif; }
        .logo { position: absolute; left: 0; top: 0; width: 75px; height: auto; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-info { font-size: 11pt; margin: 2px 0; }
        
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin: 20px 0; font-size: 14pt; text-decoration: underline; }
        
        .meta-info { margin-bottom: 15px; font-family: Arial, sans-serif; font-size: 11pt; }
        .meta-info td { padding: 3px 10px 3px 0; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-family: Arial, sans-serif; }
        .table th, .table td { border: 1px solid #000; padding: 6px; text-align: center; font-size: 10pt; vertical-align: middle; }
        .table th { background-color: #e0e0e0; font-weight: bold; }
        .text-left { text-align: left !important; padding-left: 8px; }
        
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        @if($tenant->logo_path)
            @php
                $path = public_path('uploads/' . $tenant->logo_path);
                $imageData = null;
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $imageData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
                }
            @endphp
            @if($imageData) <img src="{{ $imageData }}" class="logo"> @endif
        @endif
        
        <div class="school-name">{{ $tenant->name }}</div>
        <div class="school-info" style="font-weight: bold;">NPSN: {{ $tenant->npsn }}</div>
        <div class="school-info" style="font-size: 10pt;">{{ $tenant->address }}</div>
    </div>

    <div class="title">REKAPITULASI HASIL ASESMEN PEMINATAN / PENJURUSAN (RIASEC)</div>

    <table class="meta-info">
        <tr>
            <td><strong>Kelas</strong></td><td>: {{ $classroom->name }}</td>
            <td width="50px"></td>
            <td><strong>Paket Asesmen</strong></td><td>: {{ $package->title }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2" width="4%">No</th>
                <th rowspan="2" width="18%">Nama Siswa</th>
                <th rowspan="2" width="10%">NISN</th>
                <th colspan="6">Skor Tipe Kepribadian Holland</th>
                <th rowspan="2" width="10%">Kode Holland<br>(Top 3)</th>
                <th rowspan="2" width="20%">Rekomendasi<br>Peminatan / Jurusan</th>
            </tr>
            <tr>
                <th width="5%">R</th>
                <th width="5%">I</th>
                <th width="5%">A</th>
                <th width="5%">S</th>
                <th width="5%">E</th>
                <th width="5%">C</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $row->student->name }}</td>
                    <td>{{ $row->student->nisn }}</td>
                    
                    <td>{{ $row->score_r }}</td>
                    <td>{{ $row->score_i }}</td>
                    <td>{{ $row->score_a }}</td>
                    <td>{{ $row->score_s }}</td>
                    <td>{{ $row->score_e }}</td>
                    <td>{{ $row->score_c }}</td>
                    
                    <td style="font-weight: bold; letter-spacing: 2px;">
                        {{ $row->riasec_result ? $row->riasec_result['code'] : '-' }}
                    </td>
                    <td style="font-weight: bold;">
                        {{ $row->riasec_result ? $row->riasec_result['recommendation'] : '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 9pt; margin-top: 10px;">
        <strong>Keterangan Tipe:</strong> 
        R (Realistic/Realistis), I (Investigative/Penyelidik), A (Artistic/Artistik), S (Social/Sosial), E (Enterprising/Pengusaha), C (Conventional/Konvensional).
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Guru Bimbingan Konseling</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>