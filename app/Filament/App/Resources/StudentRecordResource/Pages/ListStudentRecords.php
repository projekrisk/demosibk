<?php

namespace App\Filament\App\Resources\StudentRecordResource\Pages;

use App\Filament\App\Resources\StudentRecordResource;
use App\Models\StudentRecord;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Facades\Filament;
use Barryvdh\DomPDF\Facade\Pdf;

class ListStudentRecords extends ListRecords
{
    protected static string $resource = StudentRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Cetak PDF (Dipindahkan ke sini)
            Actions\Action::make('cetak_rekap')
                ->label('Cetak Rekapitulasi')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth())
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now()->endOfMonth())
                        ->required(),
                    Forms\Components\Select::make('type')
                        ->label('Filter Jenis (Opsional)')
                        ->options([
                            'achievement' => 'Prestasi',
                            'violation' => 'Pelanggaran',
                            'other' => 'Lainnya',
                        ])
                        ->placeholder('Semua Jenis'),
                ])
                ->action(function (array $data) {
                    $tenant = Filament::getTenant();
                    
                    // Query Catatan Siswa
                    $query = StudentRecord::query()
                        ->whereHas('student', fn ($q) => $q->where('tenant_id', $tenant->id))
                        ->whereBetween('recorded_at', [$data['start_date'], $data['end_date']]);

                    // Filter Jenis jika dipilih
                    if (!empty($data['type'])) {
                        $query->where('type', $data['type']);
                    }

                    $records = $query->with('student')->orderBy('recorded_at', 'desc')->get();

                    // Load View PDF
                    $pdf = Pdf::loadView('pdf.student-records-recap', [
                        'records' => $records,
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'tenant' => $tenant,
                        'filter_type' => $data['type'] ?? 'Semua',
                    ])->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Rekap-Catatan-Siswa-' . now()->format('Ymd') . '.pdf');
                }),

            // 2. Tombol Create (Bawaan)
            Actions\CreateAction::make()
                ->label('Catat Baru'),
        ];
    }
}