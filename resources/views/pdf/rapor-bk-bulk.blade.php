<!DOCTYPE html>
<html>
<head>
    <title>Cetak Massal Rapor BK</title>
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @foreach($reports as $report)
        {{-- Include Template Rapor Satuan dengan data titimangsa --}}
        @include('pdf.rapor-bk', [
            'report' => $report,
            'student' => $report->student,
            'tenant' => $tenant,
            'records' => $report->student->records,
            'signing_place' => $signing_place,
            'signing_date' => $signing_date
        ])

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>