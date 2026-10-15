<?php

namespace App\Filament\App\Resources\DailyJournalResource\Pages;

use App\Filament\App\Resources\DailyJournalResource;
use App\Models\DailyJournal;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ListDailyJournals extends ListRecords
{
    protected static string $resource = DailyJournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetak_pdf')
                ->label('Rekap PDF')
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
                ])
                ->action(function (array $data) {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    $user = Auth::user();

                    $query = DailyJournal::query()
                        ->where('tenant_id', $tenant->id)
                        ->whereBetween('date', [$data['start_date'], $data['end_date']]);

                    if ($user->role === 'konselor') {
                        $query->where('user_id', $user->id);
                    }

                    $journals = $query->orderBy('date')->get();
                    
                    $pdf = Pdf::loadView('pdf.jurnal', [
                        'journals' => $journals,
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'tenant' => $tenant,
                        'user' => $user,
                    ])->setPaper('a4', 'portrait');

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'Jurnal-Kegiatan-' . now()->format('Ymd') . '.pdf');
                }),

            Actions\CreateAction::make()
                ->label('Buat Jurnal Baru'),
        ];
    }
}