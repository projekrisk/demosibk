<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemAnnouncementResource\Pages;
use App\Models\SystemAnnouncement;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemAnnouncementResource extends Resource
{
    protected static ?string $model = SystemAnnouncement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $slug = 'pengumuman';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Konten Pengumuman')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('color')
                            ->label('Warna Banner')
                            ->options([
                                'info' => 'Info (Biru)',
                                'success' => 'Sukses (Hijau)',
                                'warning' => 'Peringatan (Kuning)',
                                'danger' => 'Penting (Merah)',
                            ])
                            ->default('info')
                            ->required(),

                        Forms\Components\Textarea::make('content')
                            ->label('Isi Pesan')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Pengumuman')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('color')
                    ->badge(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
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
            'index' => Pages\ListSystemAnnouncements::route('/'),
            'create' => Pages\CreateSystemAnnouncement::route('/create'),
            'edit' => Pages\EditSystemAnnouncement::route('/{record}/edit'),
        ];
    }
}