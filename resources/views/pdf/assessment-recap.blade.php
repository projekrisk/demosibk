<!DOCTYPE html>
<html>
<head>
    <title>Rekapitulasi Asesmen Diagnostik</title>
    <style>
        @page { size: 215mm 330mm; margin: 1.5cm; } /* Ukuran F4 */
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; }
        
        /* Kop Surat Standard */
        .header { position: relative; text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; min-height: 80px; font-family: Arial, sans-serif;}
        .logo { position: absolute; left: 0; top: 0; width: 75px; height: auto; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-info { font-size: 11pt; margin: 2px 0; }
        
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin: 20px 0; font-size: 14pt; text-decoration: underline; }
        
        /* Layout Tabel Profil */
        .summary-container { margin-bottom: 25px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-family: Arial, sans-serif; }
        .summary-table th, .summary-table td { border: 1px solid #000; padding: 8px; text-align: center; }
        .summary-table th { background-color: #e0e0e0; font-weight: bold; }
        .conclusion-box { border: 2px solid #000; padding: 10px; background-color: #fafafa; font-weight: bold; text-align: center; }
        
        /* Layout Tabel Rincian Siswa */
        .data-table { width: 100%; border-collapse: collapse; font-size: 10pt; font-family: Arial, sans-serif; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; text-align: center; }
        .data-table th { background-color: #f0f0f0; }
        .text-left { text-align: left !important; padding-left: 8px; }
        
        .section-title { font-weight: bold; margin: 20px 0 10px 0; text-transform: uppercase; }
        
        .footer { margin-top: 40px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        @if(isset($tenant) && $tenant->logo_path)
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
        
        <div class="school-name">{{ isset($tenant) ? $tenant->name : 'SIBK-digi' }}</div>
        <div class="school-info" style="font-weight: bold;">NPSN: {{ isset($tenant) ? $tenant->npsn : '-' }}</div>
        <div class="school-info" style="font-size: 10pt;">{{ isset($tenant) ? $tenant->address : '-' }}</div>
    </div>

    <div class="title">LAPORAN HASIL ASESMEN DIAGNOSTIK KEBUTUHAN PESERTA DIDIK</div>
    
    <table style="width: 100%; margin-bottom: 15px; font-size: 11pt; font-family: Arial, sans-serif;">
        <tr>
            <!-- PERBAIKAN: Menggunakan fungsi pengecekan absolut (isset) agar tidak error jika variabel tidak dikirim -->
            <td width="15%"><strong>Kelas</strong></td><td width="35%">: {{ isset($classroom) && $classroom ? $classroom->name : 'Semua Kelas' }}</td>
            <td width="15%"><strong>Paket</strong></td><td width="35%">: {{ isset($package) && $package ? $package->title : 'Semua Paket' }}</td>
        </tr>
    </table>

    <!-- A. PROFIL KELAS -->
    <div class="section-title">A. ANALISIS PROFIL KELAS</div>
    <div class="summary-container">
        <table class="summary-table">
            <thead>
                <tr>
                    <th width="25%">Rata-rata Pribadi</th>
                    <th width="25%">Rata-rata Sosial</th>
                    <th width="25%">Rata-rata Belajar</th>
                    <th width="25%">Rata-rata Karir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 16pt;">{{ $averages['pribadi'] ?? 0 }}%</td>
                    <td style="font-size: 16pt;">{{ $averages['sosial'] ?? 0 }}%</td>
                    <td style="font-size: 16pt;">{{ $averages['belajar'] ?? 0 }}%</td>
                    <td style="font-size: 16pt;">{{ $averages['karir'] ?? 0 }}%</td>
                </tr>
            </tbody>
        </table>
        
        <div class="conclusion-box">
            Berdasarkan hasil rata-rata pengisian asesmen oleh siswa kelas {{ isset($classroom) && $classroom ? $classroom->name : 'ini' }}, 
            titik fokus pendampingan yang perlu diprioritaskan saat ini berada pada bidang layanan: <br>
            <span style="font-size: 16pt; text-decoration: underline; color: #b30000;">{{ $priority_area ?? '-' }}</span>
        </div>
    </div>

    <!-- B. RINCIAN PER SISWA -->
    <div class="section-title">B. RINCIAN DATA MASALAH PER SISWA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Siswa</th>
                <th width="15%">NISN</th>
                <th width="11%">Pribadi</th>
                <th width="11%">Sosial</th>
                <th width="11%">Belajar</th>
                <th width="11%">Karir</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($assessments) && $assessments->isNotEmpty())
                @foreach($assessments as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $row->student->name ?? '-' }}</td>
                        <td>{{ $row->student->nisn ?? '-' }}</td>
                        
                        <!-- Kondisi pewarnaan manual pakai HTML -->
                        <td style="background-color: {{ $row->percentage_pribadi > 50 ? '#ffcccc' : 'transparent' }}">
                            {{ $row->percentage_pribadi }}%
                        </td>
                        <td style="background-color: {{ $row->percentage_sosial > 50 ? '#ffcccc' : 'transparent' }}">
                            {{ $row->percentage_sosial }}%
                        </td>
                        <td style="background-color: {{ $row->percentage_belajar > 50 ? '#ffcccc' : 'transparent' }}">
                            {{ $row->percentage_belajar }}%
                        </td>
                        <td style="background-color: {{ $row->percentage_karir > 50 ? '#ffcccc' : 'transparent' }}">
                            {{ $row->percentage_karir }}%
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">Belum ada data asesmen dari siswa untuk periode ini.</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <div style="font-size: 9pt; margin-top: 5px;">
        * Catatan: Kolom berwarna merah menandakan indikasi hambatan lebih dari 50%.
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>Mengetahui,<br>Guru Bimbingan Konseling</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline;">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>