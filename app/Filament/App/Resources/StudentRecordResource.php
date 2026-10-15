<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentRecordResource\Pages;
use App\Models\StudentRecord;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class StudentRecordResource extends Resource
{
    protected static ?string $model = StudentRecord::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star'; 
    protected static ?string $modelLabel = 'Prestasi & Pelanggaran';
    protected static ?string $pluralModelLabel = 'Catatan Siswa';
    protected static string | \UnitEnum | null $navigationGroup = 'Layanan BK';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'catatan-siswa';

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('student', function ($query) {
            $query->where('tenant_id', Filament::getTenant()->id);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Input Catatan')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Nama Siswa')
                            ->relationship(
                                'student', 
                                'name',
                                fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->class_name} (NIS: {$record->nis})")
                            ->searchable(['name', 'nis', 'nisn'])
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Catatan')
                            ->options([
                                'achievement' => 'Prestasi (Positif)',
                                'violation' => 'Pelanggaran (Negatif)',
                                'other' => 'Lainnya (Administrasi/Catatan Umum)',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('recorded_at')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul / Kasus')
                            ->placeholder('Contoh: Juara 1 Lomba / Terlambat')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan Detail')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('recorded_by')
                            ->default(fn () => Auth::id()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('recorded_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (StudentRecord $record) => $record->student->class_name ?? '-'),

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
            ])
            ->defaultSort('recorded_at', 'desc')
            // HEADER ACTIONS DIHAPUS DARI SINI (Sudah dipindah ke List Page)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentRecords::route('/'),
            'create' => Pages\CreateStudentRecord::route('/create'),
            'edit' => Pages\EditStudentRecord::route('/{record}/edit'),
        ];
    }
}