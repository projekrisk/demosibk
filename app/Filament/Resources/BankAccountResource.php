<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Schemas\Schema;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';
    protected static string | \UnitEnum | null $navigationGroup = 'Sistem';
    protected static ?string $modelLabel = 'Rekening Bank';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('bank_name')
                        ->label('Nama Bank')
                        ->placeholder('Contoh: Bank BCA')
                        ->required(),
                    
                    Forms\Components\TextInput::make('account_number')
                        ->label('Nomor Rekening')
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('account_holder')
                        ->label('Atas Nama')
                        ->required(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bank_name')->label('Bank')->sortable()->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('account_number')->label('No. Rekening')->copyable(),
                Tables\Columns\TextColumn::make('account_holder')->label('Atas Nama'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Status'),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}