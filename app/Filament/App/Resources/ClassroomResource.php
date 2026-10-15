<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use App\Models\Level;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;
    
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $modelLabel = 'Data Kelas';
    protected static ?string $pluralModelLabel = 'Data Kelas';
    protected static ?int $navigationSort = 1; 

    protected static ?string $slug = 'kelas';

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function canCreate(): bool
    {
        return in_array(Auth::user()->role, ['super_admin', 'admin_sekolah']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kelas')
                            ->placeholder('Contoh: X-E1')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('level_id')
                            ->label('Tingkat')
                            ->relationship(
                                'level', 
                                'name',
                                fn ($query) => $query->where('tenant_id', Filament::getTenant()->id)
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Tingkat Baru')
                                    ->required(),
                                Forms\Components\Hidden::make('tenant_id')
                                    ->default(Filament::getTenant()->id),
                            ])
                            ->required(),
                            
                        Forms\Components\Hidden::make('tenant_id')
                            ->default(Filament::getTenant()->id),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Classroom::query()
                    ->where('tenant_id', Filament::getTenant()->id);

                $user = Auth::user();
                
                if ($user->role === 'konselor') {
                    $query->whereHas('counselors', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
                }

                return $query->orderByRaw('LENGTH(name) ASC, name ASC');
            })
            ->columns([
                Tables\Columns\TextColumn::make('level.name')
                    ->sortable()
                    ->label('Tingkat')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Kelas')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Jumlah Siswa')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('level_id')
                    ->label('Filter Tingkat')
                    ->options(fn () => Level::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),
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

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}