<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CounselingServiceResource;
use App\Models\CounselingService;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestScheduled extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected static ?string $heading = 'Agenda Konseling Mendatang';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CounselingService::query()
                    ->where('tenant_id', Filament::getTenant()->id)
                    ->where('status', 'ongoing')
                    ->where('occurred_at', '>=', now())
                    ->withCount('students')
                    ->orderBy('occurred_at', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Waktu')
                    ->dateTime('d M, H:i') 
                    ->sortable(),

                Tables\Columns\TextColumn::make('sasaran_display')
                    ->label('Siswa / Sasaran')
                    ->limit(20)
                    ->state(fn (CounselingService $record) => match ($record->service_type) {
                        'Bimbingan Klasikal' => $record->target_class,
                        'Bimbingan Kelompok', 'Konseling Kelompok' => 'Kelompok (' . $record->students_count . ' Org)',
                        default => $record->student?->name
                    })
                    ->description(fn (CounselingService $record) => match ($record->service_type) {
                        'Bimbingan Klasikal' => 'Klasikal',
                        'Bimbingan Kelompok', 'Konseling Kelompok' => 'Layanan Kelompok',
                        default => null 
                    }),

                Tables\Columns\TextColumn::make('service_type')
                    ->label('Layanan')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Konseling Individu' => 'Individu',
                        'Bimbingan Klasikal' => 'Klasikal',
                        'Bimbingan Kelompok' => 'Bim. Kelompok',
                        'Konseling Kelompok' => 'Kon. Kelompok',
                        default => 'Lainnya',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton()
                    ->tooltip('Edit / Detail')
                    ->url(fn (CounselingService $record): string => CounselingServiceResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (CounselingService $record) => 
                        Auth::user()->role === 'admin_sekolah' || 
                        Auth::user()->role === 'super_admin' ||
                        $record->counselor_id === Auth::id()
                    ),
            ])
            ->paginated(false);
    }
}