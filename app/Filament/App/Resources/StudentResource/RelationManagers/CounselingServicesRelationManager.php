<?php

namespace App\Filament\App\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\CounselingService;
use Illuminate\Support\Facades\Auth;

use Filament\Schemas\Schema;

class CounselingServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'counselingServices';

    protected static ?string $title = 'Riwayat';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('topic')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('topic')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('student_class')
                    ->label('Kelas Saat Itu')
                    ->sortable(),

                Tables\Columns\TextColumn::make('service_type')
                    ->label('Layanan')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('topic')
                    ->label('Topik Masalah')
                    ->limit(30)
                    ,

                Tables\Columns\TextColumn::make('counselor.name')
                    ->label('Konselor')
                    ->icon('heroicon-m-user')
                    ->description(fn (CounselingService $record) => $record->counselor_id === Auth::id() ? '(Anda)' : ''),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'ongoing' => 'warning',
                        'pending' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->visible(fn (CounselingService $record) => 
                        Auth::user()->role === 'admin_sekolah' || 
                        Auth::user()->role === 'super_admin' ||
                        $record->counselor_id === Auth::id()
                    )
                    ->url(fn (CounselingService $record): string => route('filament.app.resources.layanan.edit', [
                        'record' => $record, 
                        'tenant' => \Filament\Facades\Filament::getTenant()->slug
                    ])),
            ])
            ->bulkActions([
                //
            ]);
    }
}