<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DailyJournalResource\Pages;
use App\Models\DailyJournal;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DailyJournalResource extends Resource
{
    protected static ?string $model = DailyJournal::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?string $modelLabel = 'Jurnal Harian';
    protected static ?string $pluralModelLabel = 'Jurnal Guru';
    protected static string | \UnitEnum | null $navigationGroup = 'Layanan BK';
    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'jurnal';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'konselor') {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Log Aktivitas')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),

                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->numeric()
                            ->default(60)
                            ->required(),

                        Forms\Components\Textarea::make('activity')
                            ->label('Uraian Aktivitas')
                            ->rows(5)
                            ->placeholder('Contoh: Melakukan konseling individu dengan siswa kelas XII...')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Guru BK')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->role !== 'konselor'),

                Tables\Columns\TextColumn::make('activity')
                    ->label('Aktivitas')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' Menit')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailyJournals::route('/'),
            'create' => Pages\CreateDailyJournal::route('/create'),
            'edit' => Pages\EditDailyJournal::route('/{record}/edit'),
        ];
    }
}