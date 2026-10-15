<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $modelLabel = 'Data Konselor';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'konselor';

    protected static ?string $tenantOwnershipRelationshipName = 'tenants';
    protected static ?string $tenantRelationshipName = 'members';

    public static function canCreate(): bool
    {
        $user = Auth::user();
        return in_array($user->role, ['super_admin', 'admin_sekolah']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->description(function() {
                        $tenant = Filament::getTenant();
                        $limit = $tenant->getCounselorLimit();
                        $current = $tenant->members()->where('role', 'konselor')->count();
                        
                        $limitText = $limit === null ? 'Tak Terbatas' : $limit;
                        return "Penggunaan Kuota Konselor: {$current} dari {$limitText} akun.";
                    })
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('628...')
                            ->maxLength(20),
                        
                        Forms\Components\Select::make('role')
                            ->label('Jabatan / Peran')
                            ->options([
                                'konselor' => 'Konselor (Guru BK)',
                                'admin_sekolah' => 'Admin Sekolah (Operator)',
                            ])
                            ->default('konselor')
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context): string => $context === 'edit' ? 'Kosongkan jika tidak ingin mengubah password.' : ''),

                        Forms\Components\Select::make('classrooms')
                            ->label('Kelas Binaan (Siswa Asuh)')
                            ->relationship(
                                'classrooms', 
                                'name',
                                fn (Builder $query) => $query
                                    ->where('tenant_id', Filament::getTenant()->id)
                                    ->orderByRaw('LENGTH(name) ASC, name ASC')
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Pilih kelas yang akan ditangani oleh konselor ini.')
                            ->visible(fn (Forms\Get $get) => $get('role') === 'konselor'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin_sekolah' => 'danger',
                        'konselor' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                
                Tables\Columns\TextColumn::make('classrooms_count')
                    ->label('Jml Kelas')
                    ->counts('classrooms')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin_sekolah' => 'Admin Sekolah',
                        'konselor' => 'Konselor',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Password')
                        ->icon('heroicon-m-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Password Pengguna')
                        ->modalDescription('Apakah Anda yakin ingin menghapus password user ini? Password akan dikosongkan (NULL).')
                        ->action(function (User $record) {
                            $record->update(['password' => null]);
                            
                            Notification::make()
                                ->title('Password Direset')
                                ->body("Password untuk {$record->name} telah dikosongkan.")
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),

                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}