<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssessmentPackageResource\Pages;
use App\Filament\App\Resources\AssessmentPackageResource\RelationManagers;
use App\Models\AssessmentPackage;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class AssessmentPackageResource extends Resource
{
    protected static ?string $model = AssessmentPackage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $modelLabel = 'Paket Instrumen';
    protected static ?string $pluralModelLabel = 'Paket Instrumen Asesmen';
    protected static string | \UnitEnum | null $navigationGroup = 'Asesmen Kebutuhan';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'paket-asesmen';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Paket')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Nama Paket / Judul')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('category')
                            ->label('Kategori Umum')
                            ->options([
                                'AKPD' => 'AKPD (Asesmen Kebutuhan Peserta Didik)',
                                'DCM' => 'DCM (Daftar Cek Masalah)',
                                'Peminatan' => 'Peminatan / Penjurusan (RIASEC)',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->default('AKPD')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('usage_type')
                            ->label('Fungsi Angket')
                            ->options([
                                'diagnostic' => 'Asesmen Kebutuhan (Diagnostik Awal)',
                                'evaluation' => 'Evaluasi / Data Rapor (Akhir)',
                                'riasec' => 'Peminatan Penjurusan (RIASEC)',
                            ])
                            ->default('diagnostic')
                            ->required()
                            ->native(false)
                            ->helperText('Pilih "Peminatan Penjurusan" jika ini adalah instrumen Holland/RIASEC.'),

                        // FITUR BARU: MULTI-SELECT TARGET KELAS
                        Forms\Components\Select::make('target_classrooms')
                            ->label('Target Kelas')
                            ->multiple()
                            ->options(fn () => Classroom::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id'))
                            ->placeholder('Semua Kelas (Biarkan kosong)')
                            ->helperText('Pilih kelas tertentu jika paket ini HANYA untuk kelas spesifik.')
                            ->preload(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Instruksi Pengerjaan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Waktu & Status')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Rilis (Mulai)')
                            ->helperText('Biarkan kosong jika ingin langsung diterbitkan.'),
                            
                        Forms\Components\DateTimePicker::make('deadline_at')
                            ->label('Batas Waktu (Deadline)')
                            ->helperText('Biarkan kosong jika tidak ada batas waktu.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Paket')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Paket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('usage_type')
                    ->label('Fungsi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'diagnostic' => 'Diagnostik',
                        'evaluation' => 'Rapor/Evaluasi',
                        'riasec' => 'RIASEC',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'diagnostic' => 'warning',
                        'evaluation' => 'success',
                        'riasec' => 'primary',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Jml Soal')
                    ->alignCenter()
                    ->badge(),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentPackages::route('/'),
            'create' => Pages\CreateAssessmentPackage::route('/create'),
            'edit' => Pages\EditAssessmentPackage::route('/{record}/edit'),
        ];
    }
}