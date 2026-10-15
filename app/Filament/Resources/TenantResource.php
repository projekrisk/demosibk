<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Sekolah';
    protected static ?int $navigationSort = 0;


    protected static bool $shouldSkipAuthorization = true;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Sekolah')
                    ->description('Data identitas sekolah.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Tenant::class, 'slug', ignoreRecord: true),

                        Forms\Components\TextInput::make('npsn')
                            ->label('NPSN')
                            ->required()
                            ->numeric()
                            ->length(8)
                            ->unique(Tenant::class, 'npsn', ignoreRecord: true),
                        
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo Sekolah')
                            ->image()
                            ->directory('school-logos')
                            ->disk('public_uploads')
                            ->visibility('public'),
                            
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengaturan Langganan')
                    ->description('Kelola masa aktif dan status akun sekolah ini secara manual.')
                    ->schema([
                        Forms\Components\Select::make('subscription_status')
                            ->label('Status Akun')
                            ->options([
                                'trial' => 'Trial (Uji Coba)',
                                'active' => 'Active (Premium)',
                                'inactive' => 'Inactive (Suspend)',
                                'expired' => 'Expired (Habis)',
                            ])
                            ->default('trial')
                            ->required()
                            ->native(false),
                        
                        Forms\Components\DateTimePicker::make('subscription_ends_at')
                            ->label('Masa Aktif Berakhir Pada')
                            ->native(false)
                            ->seconds(false)
                            ->suffixIcon('heroicon-m-calendar')
                            ->helperText('Kosongkan tanggal jika ingin memberikan akses selamanya (Lifetime).'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Tenant $record) => 'NPSN: ' . $record->npsn),

                Tables\Columns\TextColumn::make('subscription_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'trial' => 'warning',
                        'active' => 'success',
                        'inactive' => 'gray',
                        'expired' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('Selamanya')
                    ->description(fn (Tenant $record) => $record->subscription_ends_at ? \Carbon\Carbon::parse($record->subscription_ends_at)->diffForHumans() : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('subscription_status')
                    ->label('Filter Status')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('primary'),
                
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Admin')
                    ->icon('heroicon-m-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Admin Sekolah')
                    ->modalDescription('Apakah Anda yakin? Password Admin akan diubah menjadi kode acak baru yang akan ditampilkan setelah ini.')
                    ->action(function (Tenant $record) {
                        $admin = $record->members()
                            ->where('role', 'admin_sekolah')
                            ->first();

                        if ($admin) {
                            $newPassword = Str::random(8);
                            $admin->update(['password' => Hash::make($newPassword)]);
                            
                            Notification::make()
                                ->title('Password Berhasil Direset')
                                ->body("Password baru untuk Admin {$admin->name} adalah: **{$newPassword}**. Harap salin dan berikan kepada yang bersangkutan.")
                                ->success()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal')
                                ->body('Tidak ditemukan Admin Sekolah untuk tenant ini.')
                                ->danger()
                                ->send();
                        }
                    }),
                
                Tables\Actions\Action::make('impersonate')
                    ->label('Login Admin')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Tenant $record) {
                        $user = $record->members()
                            ->where('role', 'admin_sekolah')
                            ->first();

                        if (! $user) {
                            $user = $record->members()
                                ->where('role', 'super_admin')
                                ->first();
                        }

                        if ($user) {
                            auth()->login($user);
                            return redirect('/app/' . $record->slug);
                        }

                        Notification::make()
                            ->title('Gagal Login')
                            ->body('Tidak ditemukan User Admin di sekolah ini.')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}