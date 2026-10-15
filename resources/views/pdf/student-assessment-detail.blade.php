<!DOCTYPE html>
<html>
<head>
    <title>Hasil Asesmen Individu</title>
    <style>
        @page { size: 215mm 330mm; margin: 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11pt; color: #000; } 
        
        .header { position: relative; text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; min-height: 80px; font-family: Arial, sans-serif; }
        .logo { position: absolute; left: 0; top: 0; width: 75px; height: auto; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-info { font-size: 11pt; margin: 2px 0; }
        
        .title { text-align: center; font-weight: bold; text-transform: uppercase; margin: 20px 0; font-size: 14pt; text-decoration: underline; font-family: 'Times New Roman', serif; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-family: 'Times New Roman', serif; }
        .info-table td { padding: 3px; vertical-align: top; }
        
        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: 'Times New Roman', serif; }
        .score-table th, .score-table td { border: 1px solid #000; padding: 8px; text-align: center; }
        .score-table th { background-color: #f0f0f0; }

        .answer-table { width: 100%; border-collapse: collapse; font-family: 'Arial', sans-serif; font-size: 10pt; }
        .answer-table th, .answer-table td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        .answer-table th { background-color: #f9f9f9; text-align: center; font-weight: bold; }
        .answer-col { text-align: center; width: 15%; font-weight: bold; }
        
        .footer { margin-top: 40px; width: 100%; font-family: 'Times New Roman', serif; }
        .signature-box { float: right; width: 220px; text-align: center; }
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

    <div class="title">
        @if($package && $package->usage_type == 'riasec')
            HASIL ASESMEN PEMINATAN / PENJURUSAN
        @else
            LEMBAR HASIL ASESMEN INDIVIDU
        @endif
    </div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Nama Siswa</strong></td>
            <td width="30%">: {{ $student->name }}</td>
            <td width="20%"><strong>Tanggal Isi</strong></td>
            <td width="30%">: {{ $assessment->filled_at->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $student->class_name }}</td>
            <td><strong>Paket Instrumen</strong></td>
            <td>: {{ $package ? $package->title : '-' }}</td>
        </tr>
        <tr>
            <td><strong>NIS / NISN</strong></td>
            <td>: {{ $student->nis }} / {{ $student->nisn }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- A. PROFIL HASIL -->
    <div style="font-weight: bold; margin-bottom: 5px; font-family: 'Times New Roman', serif;">A. PROFIL HASIL ASESMEN</div>
    
    @if($package && $package->usage_type == 'riasec')
        <!-- Tampilan Khusus RIASEC -->
        <table class="score-table">
            <thead>
                <tr>
                    <th>R<br><small>Realistic</small></th>
                    <th>I<br><small>Investigative</small></th>
                    <th>A<br><small>Artistic</small></th>
                    <th>S<br><small>Social</small></th>
                    <th>E<br><small>Enterprising</small></th>
                    <th>C<br><small>Conventional</small></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 14pt;">{{ $assessment->score_r }}</td>
                    <td style="font-size: 14pt;">{{ $assessment->score_i }}</td>
                    <td style="font-size: 14pt;">{{ $assessment->score_a }}</td>
                    <td style="font-size: 14pt;">{{ $assessment->score_s }}</td>
                    <td style="font-size: 14pt;">{{ $assessment->score_e }}</td>
                    <td style="font-size: 14pt;">{{ $assessment->score_c }}</td>
                </tr>
            </tbody>
        </table>
        
        <div style="border: 2px solid #000; padding: 15px; margin-bottom: 20px; text-align: center; background-color: #fafafa;">
            <p style="margin: 0 0 10px 0; font-family: 'Times New Roman', serif;">Kecenderungan Minat Utama (Kode Holland):</p>
            <div style="font-size: 24pt; font-weight: bold; letter-spacing: 5px;">{{ $assessment->riasec_result ? $assessment->riasec_result['code'] : '-' }}</div>
            <p style="margin: 10px 0 5px 0; font-family: 'Times New Roman', serif;">Berdasarkan kode di atas, siswa ini direkomendasikan untuk mengambil peminatan/jurusan:</p>
            <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">{{ $assessment->riasec_result ? $assessment->riasec_result['recommendation'] : '-' }}</div>
        </div>
    @else
        <!-- Tampilan Khusus AKPD (Lama) -->
        <table class="score-table">
            <thead>
                <tr>
                    <th>Bidang Pribadi</th>
                    <th>Bidang Sosial</th>
                    <th>Bidang Belajar</th>
                    <th>Bidang Karir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $assessment->percentage_pribadi }}%</td>
                    <td>{{ $assessment->percentage_sosial }}%</td>
                    <td>{{ $assessment->percentage_belajar }}%</td>
                    <td>{{ $assessment->percentage_karir }}%</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($assessment->counselor_notes)
        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 20px; font-family: 'Times New Roman', serif;">
            <strong>Catatan Konselor / Tindak Lanjut:</strong><br>
            {{ $assessment->counselor_notes }}
        </div>
    @endif

    <!-- B. DETAIL JAWABAN -->
    <div style="font-weight: bold; margin-bottom: 5px; font-family: 'Arial', sans-serif;">B. DETAIL JAWABAN SISWA</div>
    <table class="answer-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kategori</th>
                <th>Pertanyaan / Pernyataan</th>
                <th class="answer-col">Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @foreach($answers as $index => $ans)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $ans->question->category ?? '-' }}</td>
                    <td>{{ $ans->question->question_text ?? '-' }}</td>
                    <td class="answer-col">
                        @if(is_numeric($ans->answer_value))
                            @if($ans->answer_value == '1') Ya / Tinggi
                            @elseif($ans->answer_value == '0') Tidak / Rendah
                            @else {{ $ans->answer_value }} @endif
                        @else
                            {{ $ans->answer_value }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

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