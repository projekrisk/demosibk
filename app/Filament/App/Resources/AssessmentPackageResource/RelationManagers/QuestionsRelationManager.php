<?php

namespace App\Filament\App\Resources\AssessmentPackageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use App\Models\AssessmentQuestion;
use OpenSpout\Reader\XLSX\Reader;

use Filament\Schemas\Schema;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Daftar Soal dalam Paket';
    protected static string | \BackedEnum | null $icon = 'heroicon-o-list-bullet';

    private static $categoryOptions = [
        'Pribadi' => 'Pribadi (AKPD)',
        'Sosial' => 'Sosial (AKPD)',
        'Belajar' => 'Belajar (AKPD)',
        'Karir' => 'Karir (AKPD)',
        'R' => 'R - Realistic / Realistis (RIASEC)',
        'I' => 'I - Investigative / Penyelidik (RIASEC)',
        'A' => 'A - Artistic / Artistik (RIASEC)',
        'S' => 'S - Social / Sosial (RIASEC)',
        'E' => 'E - Enterprising / Pengusaha (RIASEC)',
        'C' => 'C - Conventional / Konvensional (RIASEC)',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Bidang / Kategori')
                            ->options(self::$categoryOptions)
                            ->required()
                            ->searchable()
                            ->native(false),

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
                            ->live()
                            ->native(false),

                        Forms\Components\Textarea::make('question_text')
                            ->label('Butir Pernyataan')
                            ->required()
                            ->rows(3),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\Hidden::make('tenant_id')
                            ->default(fn () => Filament::getTenant()->id),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->width(40)->sortable(),
                Tables\Columns\TextColumn::make('question_text')->label('Soal')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('category')->label('Bidang')->badge()
                    ->color(fn (string $state): string => in_array($state, ['R','I','A','S','E','C']) ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('type')->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'guttman' => 'Ya/Tidak',
                        'likert' => 'Skala Sikap',
                        'rating' => 'Rating (1-10)',
                        'text' => 'Uraian',
                        default => $state,
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = Filament::getTenant()->id;
                        return $data;
                    }),
                
                Tables\Actions\Action::make('import_excel')
                    ->label('Import (.xlsx)')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Upload File Excel (.xlsx)')
                            ->disk('public_uploads')
                            ->directory('temp-imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required()
                            ->helperText('Buat file Excel biasa (.xlsx) dengan 4 kolom berurutan: Kategori, Tipe, Teks Soal, Urutan. Baris pertama (judul kolom) akan otomatis diabaikan.'),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $filePath = public_path('uploads/' . $data['file']);
                        
                        if (!file_exists($filePath)) {
                            Notification::make()->title('File tidak ditemukan')->danger()->send();
                            return;
                        }

                        try {
                            $reader = new Reader();
                            $reader->open($filePath);

                            $count = 0;
                            $tenantId = Filament::getTenant()->id;
                            $packageId = $livewire->getOwnerRecord()->id;

                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $index => $row) {
                                    if ($index === 1) continue;

                                    $cells = $row->getCells();
                                    
                                    if (count($cells) < 3) continue;

                                    $category = $cells[0]->getValue();
                                    $type = strtolower(trim($cells[1]->getValue()));
                                    $questionText = $cells[2]->getValue();
                                    
                                    $sortOrder = isset($cells[3]) && is_numeric($cells[3]->getValue()) 
                                        ? (int)$cells[3]->getValue() 
                                        : ($count + 1);

                                    if (!empty($category) && !empty($type) && !empty($questionText)) {
                                        AssessmentQuestion::create([
                                            'tenant_id' => $tenantId,
                                            'assessment_package_id' => $packageId,
                                            'category' => trim($category),
                                            'type' => $type,
                                            'question_text' => trim($questionText),
                                            'sort_order' => $sortOrder,
                                            'is_active' => true,
                                        ]);
                                        $count++;
                                    }
                                }
                            }

                            $reader->close();
                            @unlink($filePath);

                            Notification::make()
                                ->title('Import Berhasil')
                                ->body("Sebanyak {$count} butir soal telah berhasil dimasukkan.")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Membaca File Excel')
                                ->body('Error: Pastikan file yang diupload adalah format .xlsx yang valid.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}