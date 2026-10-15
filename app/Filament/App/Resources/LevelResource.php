<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LevelResource\Pages;
use App\Models\Level;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bars-3-bottom-left';
    protected static ?string $modelLabel = 'Data Tingkat';
    protected static ?string $pluralModelLabel = 'Data Tingkat';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'tingkat';

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('tenant_id', Filament::getTenant()->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Tingkat')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tingkat')
                            ->placeholder('Contoh: 10, 11, XII, Alumni')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Hidden::make('tenant_id')
                            ->default(fn () => Filament::getTenant()->id),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tingkat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('classrooms_count')
                    ->counts('classrooms')
                    ->label('Jumlah Kelas')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLevels::route('/'),
            'create' => Pages\CreateLevel::route('/create'),
            'edit' => Pages\EditLevel::route('/{record}/edit'),
        ];
    }
}