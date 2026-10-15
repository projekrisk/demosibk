<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentResource\Pages;
use App\Filament\App\Resources\StudentResource\RelationManagers\CounselingServicesRelationManager;
use App\Filament\App\Resources\StudentResource\RelationManagers\StudentRecordsRelationManager;
use App\Models\Student;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use App\Filament\Imports\StudentImporter;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Peserta Didik';
    protected static ?string $pluralModelLabel = 'Data Peserta';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'siswa';
    
    protected static ?string $recordTitleAttribute = 'name';


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'nisn', 'nis'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Student $record */
        return [
            'Kelas' => $record->classroom?->name ?? 'Belum ada kelas',
            'NISN' => $record->nisn,
            'NIS' => $record->nis,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Pages\ViewStudent::getUrl(['record' => $record]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('tenant_id', Filament::getTenant()->id);
        
        $user = Auth::user();
        
        if ($user->role === 'konselor') {
            $query->whereHas('classroom', function (Builder $q) use ($user) {
                $q->whereHas('counselors', function (Builder $q2) use ($user) {
                    $q2->where('users.id', $user->id);
                });
            });
        }

        return $query;
    }
    

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Identitas Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->numeric()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->numeric()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('classroom_id')
                            ->label('Kelas')
                            ->relationship(
                                'classroom', 
                                'name',
                                fn ($query) => $query->where('tenant_id', Filament::getTenant()->id)
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->label('Nama Kelas'),
                                Forms\Components\Select::make('level')
                                    ->label('Tingkat')
                                    ->options(['10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12'])
                                    ->required(),
                                Forms\Components\Hidden::make('tenant_id')->default(Filament::getTenant()->id),
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Status Siswa')
                            ->options([
                                'Aktif' => 'Aktif',
                                'Pantauan' => 'Dalam Pantauan',
                                'Alumni' => 'Alumni',
                                'Keluar' => 'Keluar / Pindah',
                            ])
                            ->default('Aktif')
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum ada kelas'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Pantauan' => 'warning',
                        'Alumni' => 'gray',
                        'Keluar' => 'danger',
                        default => 'info',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->label('Filter Kelas')
                    ->relationship('classroom', 'name', function (Builder $query) {
                        $tenantId = Filament::getTenant()->id;
                        $user = Auth::user();

                        $query->where('tenant_id', $tenantId);

                        if ($user->role === 'konselor') {
                            $query->whereHas('counselors', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            });
                        }

                        return $query->orderByRaw('LENGTH(name) ASC, name ASC');
                    })
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Pantauan' => 'Dalam Pantauan',
                    ]),
            ])
            
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('reset_password')
                        ->label('Reset Pass')
                        ->icon('heroicon-m-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Password Siswa')
                        ->modalDescription('Apakah Anda yakin ingin menghapus password siswa ini? Siswa harus melakukan aktivasi ulang di aplikasi Android untuk membuat password baru.')
                        ->action(function (Student $record) {
                            $record->update(['password' => null]);
                            
                            Notification::make()
                                ->title('Password Berhasil Direset')
                                ->body('Password siswa telah dikosongkan. Siswa dapat melakukan aktivasi ulang sekarang.')
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

    public static function getRelations(): array
    {
        return [
            CounselingServicesRelationManager::class,
            StudentRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}