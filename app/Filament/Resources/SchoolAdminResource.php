<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolAdminResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    
    protected static ?string $modelLabel = 'Admin Sekolah';
    
    protected static ?string $pluralModelLabel = 'Admin Sekolah';

    protected static ?string $slug = 'admin-sekolah';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'admin_sekolah');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Forms\Components\Select::make('tenants')
                            ->label('Tugaskan ke Sekolah')
                            ->relationship('tenants', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Pilih sekolah yang akan dikelola oleh admin ini.'),
                        
                        Forms\Components\Hidden::make('role')
                            ->default('admin_sekolah'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Admin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tenants.name')
                    ->label('Sekolah')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limitList(2),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-m-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Admin')
                    ->modalDescription('Apakah Anda yakin? Password akan diubah menjadi kode acak baru yang akan ditampilkan di layar.')
                    ->action(function (User $record) {
                        $newPassword = Str::random(8);
                        $record->update(['password' => Hash::make($newPassword)]);
                        
                        Notification::make()
                            ->title('Password Berhasil Direset')
                            ->body("Password baru untuk **{$record->name}** adalah: \n\n**{$newPassword}**\n\nHarap salin dan berikan kepada admin yang bersangkutan.")
                            ->success()
                            ->persistent()
                            ->send();
                    }),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolAdmins::route('/'),
            'create' => Pages\CreateSchoolAdmin::route('/create'),
            'edit' => Pages\EditSchoolAdmin::route('/{record}/edit'),
        ];
    }
}