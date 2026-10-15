<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssessmentQuestionResource\Pages;
use App\Models\AssessmentQuestion;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Forms\Get; 
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class AssessmentQuestionResource extends Resource
{
    protected static ?string $model = AssessmentQuestion::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $modelLabel = 'Butir Instrumen';
    protected static ?string $pluralModelLabel = 'Bank Soal Asesmen';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Asesmen Kebutuhan';
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'bank-soal';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Detail Pertanyaan')
                    ->description('Sesuaikan jenis pertanyaan dengan instrumen (AKPD, DCM, atau AUM).')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Bidang Layanan / Tipe')
                            ->options([
                                'Pribadi' => 'Pribadi',
                                'Sosial' => 'Sosial',
                                'Belajar' => 'Belajar',
                                'Karir' => 'Karir',
                                'R' => 'R - Realistic (Realistis)',
                                'I' => 'I - Investigative (Penyelidik)',
                                'A' => 'A - Artistic (Artistik)',
                                'S' => 'S - Social (Sosial)',
                                'E' => 'E - Enterprising (Pengusaha)',
                                'C' => 'C - Conventional (Konvensional)',
                            ])
                            ->required()
                            ->searchable()
                            ->native(false),

                        // UPDATE: Opsi Tipe Soal disesuaikan dengan Aplikasi Android
                        Forms\Components\Select::make('type')
                            ->label('Format Jawaban')
                            ->options([
                                'guttman' => 'Pilihan Tegas (Ya / Tidak)',
                                'likert' => 'Skala Sikap (Sangat Sesuai - Tidak Sesuai)',
                                'rating' => 'Skala Rating (Angka 1 - 10)',
                                'text' => 'Pertanyaan Terbuka (Uraian)',
                            ])
                            ->default('guttman')
                            ->required()
                            ->native(false)
                            ->live() 
                            ->helperText(fn (Get $get) => match ($get('type')) {
                                'guttman' => 'Cocok untuk DCM/AKPD. Pilihan: Ya (1) atau Tidak (0).',
                                'likert' => 'Cocok untuk AUM. Pilihan: SS, S, KS, TS.',
                                'rating' => 'Siswa memilih nilai dari 1 (Rendah) sampai 10 (Tinggi).',
                                'text' => 'Siswa dapat mengetik jawaban bebas.',
                                default => 'Pilih format jawaban.',
                            }),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('No. Urut')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\Textarea::make('question_text')
                            ->label('Butir Pernyataan / Pertanyaan')
                            ->placeholder('Contoh: Saya merasa cemas ketika berbicara di depan umum.')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Tampilkan Soal Ini')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('question_text')
                    ->label('Pernyataan')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Bidang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pribadi' => 'danger',
                        'Sosial' => 'warning',
                        'Belajar' => 'info',
                        'Karir' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                
                // Menampilkan Tipe Soal dengan Label yang mudah dibaca
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'guttman' => 'Ya/Tidak',
                        'checklist' => 'Ya/Tidak', // Backward compatibility
                        'likert' => 'Skala (SS-TS)',
                        'scale' => 'Skala (SS-TS)', // Backward compatibility
                        'rating' => 'Rating (1-10)',
                        'text' => 'Uraian',
                        default => $state,
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Filter Bidang')
                    ->options([
                        'Pribadi' => 'Pribadi',
                        'Sosial' => 'Sosial',
                        'Belajar' => 'Belajar',
                        'Karir' => 'Karir',
                    ]),
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'guttman' => 'Ya/Tidak',
                        'likert' => 'Skala Sikap',
                        'rating' => 'Rating',
                        'text' => 'Uraian',
                    ]),
            ])
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
            'index' => Pages\ListAssessmentQuestions::route('/'),
            'create' => Pages\CreateAssessmentQuestion::route('/create'),
            'edit' => Pages\EditAssessmentQuestion::route('/{record}/edit'),
        ];
    }
}