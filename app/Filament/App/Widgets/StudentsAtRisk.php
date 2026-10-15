<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\StudentResource;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StudentsAtRisk extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Status Pantauan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->where('tenant_id', Filament::getTenant()->id)
                    ->where('status', 'Pantauan')
                    ->withCount('counselingServices')
            )
            ->defaultSort('counseling_services_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->weight('bold')
                    ->description(fn (Student $record) => $record->nisn),
                
                Tables\Columns\TextColumn::make('class_name')
                    ->label('Kelas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('counseling_services_count')
                    ->label('Frekuensi Konseling')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 5 => 'danger',
                        $state > 2 => 'warning',
                        default => 'info',
                    })
                    ->suffix(' kali'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->since()
                    ->color('gray')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Lihat Riwayat')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->button()
                    ->size('xs')
                    ->url(fn (Student $record): string => StudentResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}