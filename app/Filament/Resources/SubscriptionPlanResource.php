<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?string $modelLabel = 'Paket Langganan';
    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'paket';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Detail Paket')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->placeholder('Contoh: Paket Tahunan'),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('duration_label')
                            ->label('Label Durasi (Teks)')
                            ->placeholder('/ tahun')
                            ->required(),

                        Forms\Components\TextInput::make('duration_in_days')
                            ->label('Durasi Aktif (Hari)')
                            ->numeric()
                            ->suffix('Hari')
                            ->required()
                            ->helperText('Contoh: 14 untuk Trial, 180 untuk Semester, 365 untuk Tahunan.'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Fitur & Tampilan')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->label('Daftar Fitur')
                            ->simple(
                                Forms\Components\TextInput::make('feature')
                                    ->placeholder('Contoh: Unlimited Konselor')
                                    ->required(),
                            )
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Jadikan Paket Unggulan (Highlight)'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Paket')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
                Tables\Columns\TextColumn::make('duration_in_days')->suffix(' Hari')->label('Durasi'), 
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Unggulan'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
                Tables\Columns\TextColumn::make('sort_order')->sortable()->label('Urutan'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}