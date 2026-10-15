<?php

namespace App\Filament\App\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF
use Filament\Facades\Filament; // Import Filament

use Filament\Schemas\Schema;

class StudentRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'records';

    // Mengubah judul tab agar lebih singkat dan profesional
    protected static ?string $title = 'Catatan Siswa';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Jenis')
                    ->options([
                        'achievement' => 'Prestasi (Positif)',
                        'violation' => 'Pelanggaran (Negatif)',
                        'other' => 'Lainnya (Catatan Umum)',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->placeholder('Contoh: Juara 1 Lomba')
                    ->required()
                    ->maxLength(255),

                // Kolom Point DIHAPUS agar konsisten

                Forms\Components\DatePicker::make('recorded_at')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
                
                Forms\Components\Hidden::make('recorded_by')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('recorded_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'achievement' => 'success', 
                        'violation' => 'danger',   
                        'other' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'achievement' => 'PRESTASI',
                        'violation' => 'PELANGGARAN',
                        'other' => 'LAINNYA',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),

                // Kolom Point DIHAPUS dari tabel ini juga

                Tables\Columns\TextColumn::make('recorder.name')
                    ->label('Pencatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                // Tombol Cetak PDF
                Tables\Actions\Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function () {
                        // Ambil data siswa pemilik record ini
                        $student = $this->getOwnerRecord();
                        // Ambil record prestasi/pelanggaran
                        $records = $student->records()->orderBy('recorded_at', 'desc')->get();
                        $tenant = Filament::getTenant();

                        // Load View PDF
                        $pdf = Pdf::loadView('pdf.student-records', [
                            'student' => $student,
                            'records' => $records,
                            'tenant' => $tenant,
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Catatan-' . str_replace(' ', '-', $student->name) . '.pdf');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}