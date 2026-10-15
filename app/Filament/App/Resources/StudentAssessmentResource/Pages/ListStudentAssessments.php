<?php

namespace App\Filament\App\Resources\StudentAssessmentResource\Pages;

use App\Filament\App\Resources\StudentAssessmentResource;
use App\Models\AssessmentPackage;
use App\Models\Classroom;
use App\Models\StudentAssessment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class ListStudentAssessments extends ListRecords
{
    protected static string $resource = StudentAssessmentResource::class;

    // FITUR 1: Membuat Tab Filter di atas tabel
    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Data')
                ->badge(StudentAssessmentResource::getEloquentQuery()->count()),
            
            'akpd' => Tab::make('Asesmen Kebutuhan (AKPD)')
                ->badge(StudentAssessmentResource::getEloquentQuery()->whereHas('package', fn($q) => $q->where('usage_type', '!=', 'riasec'))->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('package', fn($q) => $q->where('usage_type', '!=', 'riasec'))),
                
            'riasec' => Tab::make('Peminatan (RIASEC)')
                ->badge(StudentAssessmentResource::getEloquentQuery()->whereHas('package', fn($q) => $q->where('usage_type', 'riasec'))->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('package', fn($q) => $q->where('usage_type', 'riasec'))),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // FITUR 2: Tombol Cetak Analisis yang Spesifik per Paket & Kelas
            Actions\Action::make('print_analysis')
                ->label('Cetak Rekap Kelas (.pdf)')
                ->icon('heroicon-m-document-arrow-down')
                ->color('danger')
                ->form([
                    Forms\Components\Select::make('assessment_package_id')
                        ->label('Pilih Paket Asesmen (Kegiatan)')
                        ->options(fn() => AssessmentPackage::where('tenant_id', Filament::getTenant()->id)->pluck('title', 'id'))
                        ->searchable()
                        ->required(),
                        
                    Forms\Components\Select::make('classroom_id')
                        ->label('Pilih Kelas')
                        ->options(fn() => Classroom::where('tenant_id', Filament::getTenant()->id)->orderByRaw('LENGTH(name) ASC, name ASC')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $tenant = Filament::getTenant();
                    $package = AssessmentPackage::find($data['assessment_package_id']);
                    $classroom = Classroom::find($data['classroom_id']);

                    // Ambil data siswa HANYA untuk paket dan kelas yang dipilih
                    $assessments = StudentAssessment::with(['student', 'package'])
                        ->where('assessment_package_id', $package->id)
                        ->whereHas('student', fn($q) => $q->where('classroom_id', $classroom->id))
                        ->where('status', 'completed')
                        ->orderBy('filled_at', 'desc')
                        ->get()
                        ->sortBy(fn($a) => $a->student->name); // Urutkan berdasarkan nama siswa A-Z
                    
                    if ($assessments->isEmpty()) {
                        Notification::make()->title('Data Kosong')->body('Belum ada siswa di kelas tersebut yang menyelesaikan paket ini.')->warning()->send();
                        return;
                    }

                    // --- LOGIKA JIKA YANG DICETAK ADALAH RIASEC ---
                    if ($package->usage_type === 'riasec') {
                        $pdf = Pdf::loadView('pdf.riasec-recap', [
                            'assessments' => $assessments,
                            'package' => $package,
                            'classroom' => $classroom,
                            'tenant' => $tenant
                        ])->setPaper('a4', 'landscape'); // Landscape agar tabel R-I-A-S-E-C muat

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Rekap-Penjurusan-RIASEC-' . str_replace(' ', '', $classroom->name) . '.pdf');
                    } 
                    
                    // --- LOGIKA JIKA YANG DICETAK ADALAH AKPD/DCM ---
                    else {
                        $count = $assessments->count();
                        $averages = ['pribadi' => 0, 'sosial' => 0, 'belajar' => 0, 'karir' => 0];
                        $priorityArea = '-';

                        if ($count > 0) {
                            $averages = [
                                'pribadi' => round($assessments->avg('percentage_pribadi')),
                                'sosial'  => round($assessments->avg('percentage_sosial')),
                                'belajar' => round($assessments->avg('percentage_belajar')),
                                'karir'   => round($assessments->avg('percentage_karir')),
                            ];
                            $maxAvg = max($averages);
                            if ($maxAvg > 0) {
                                $priorityArea = array_search($maxAvg, $averages);
                            }
                        }

                        $pdf = Pdf::loadView('pdf.assessment-recap', [
                            'assessments' => $assessments,
                            'averages' => $averages,
                            'priority_area' => ucfirst($priorityArea),
                            'tenant' => $tenant,
                            'package' => $package,
                            'classroom' => $classroom
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Rekap-AKPD-' . str_replace(' ', '', $classroom->name) . '.pdf');
                    }
                }),
        ];
    }
}