<?php

namespace App\Filament\App\Pages;

use App\Models\CounselingService;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanBulanan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | \UnitEnum | null $navigationGroup = 'Layanan BK';
    protected static ?string $title = 'Laporan';
    protected static ?int $navigationSort = 5;
    
    protected static ?string $slug = 'laporan';

    protected string $view = 'filament.app.pages.laporan-bulanan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->description('Pilih periode dan jenis layanan yang ingin dicetak.')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->required(),
                        
                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->required(),

                        Select::make('service_type')
                            ->label('Jenis Layanan (Opsional)')
                            ->placeholder('Semua Layanan')
                            ->options([
                                'Konseling Individu' => 'Konseling Individu',
                                'Bimbingan Klasikal' => 'Bimbingan Klasikal',
                                'Bimbingan Kelompok' => 'Bimbingan Kelompok',
                                'Konseling Kelompok' => 'Konseling Kelompok',
                                'Kunjungan Rumah' => 'Kunjungan Rumah',
                                'Konferensi Kasus' => 'Konferensi Kasus',
                                'Advokasi' => 'Advokasi',
                                'Bimbingan Belajar' => 'Bimbingan Belajar',
                                'Bimbingan Karir' => 'Bimbingan Karir',
                                'Bimbingan Pribadi' => 'Bimbingan Pribadi',
                                'Bimbingan Sosial' => 'Bimbingan Sosial',
                                'Alih Tangan Kasus' => 'Alih Tangan Kasus',
                                'Konsultasi' => 'Konsultasi',
                                'Kolaborasi' => 'Kolaborasi',
                            ])
                            ->searchable(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function cetak()
    {
        $data = $this->form->getState();
        $tenant = Filament::getTenant();
        $user = Auth::user();

        $query = CounselingService::query()
            ->where('tenant_id', $tenant->id)
            ->whereBetween('occurred_at', [$data['start_date'], $data['end_date']]);

        if ($user->role === 'konselor') {
            $query->where('counselor_id', $user->id);
        }

        if (!empty($data['service_type'])) {
            $query->where('service_type', $data['service_type']);
        }

        $services = $query->with(['student', 'counselor'])->orderBy('occurred_at')->get();

        Carbon::setLocale('id');

        $pdf = Pdf::loadView('pdf.laporan', [
            'tenant' => $tenant,
            'services' => $services,
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
            'filterType' => $data['service_type'] ?? 'Semua Jenis Layanan',
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-BK-' . now()->format('Ymd-His') . '.pdf');
    }
}