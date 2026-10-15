<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CounselingServiceResource\Pages;
use App\Models\CounselingService;
use App\Models\Student;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

class CounselingServiceResource extends Resource
{
    protected static ?string $model = CounselingService::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'Layanan BK';
    protected static ?string $pluralModelLabel = 'Agenda Layanan';

    protected static string | \UnitEnum | null $navigationGroup = 'Layanan BK';
    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'layanan';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
                Forms\Components\Section::make('Sasaran Layanan')
                    ->description('Tentukan siapa yang menerima layanan ini.')
                    ->schema([
                        Forms\Components\Select::make('target_class')
                            ->label('Kelas Sasaran')
                            ->options(function () {
                                $user = Auth::user();
                                $query = Classroom::query()->where('tenant_id', Filament::getTenant()->id);

                                if ($user->role === 'konselor') {
                                    $query->whereHas('counselors', function ($q) use ($user) {
                                        $q->where('users.id', $user->id);
                                    });
                                }
                                
                                return $query->orderByRaw('LENGTH(name) ASC, name ASC')
                                             ->pluck('name', 'name');
                            })
                            ->searchable()
                            ->required(fn (Get $get) => $get('service_type') === 'Bimbingan Klasikal')
                            ->visible(fn (Get $get) => $get('service_type') === 'Bimbingan Klasikal')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('students')
                            ->label('Anggota Kelompok')
                            ->relationship(
                                'students', 
                                'name',
                                function (Builder $query) {
                                    $user = Auth::user();
                                    $query->where('tenant_id', Filament::getTenant()->id);

                                    if ($user->role === 'konselor') {
                                        $classroomIds = $user->classrooms()->pluck('classrooms.id')->toArray();
                                        $query->whereIn('classroom_id', $classroomIds);
                                    }
                                    
                                    $query->orderBy('name', 'asc');
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->class_name} (NIS: {$record->nis})")
                            ->searchable(['name', 'nis', 'nisn'])
                            ->multiple()
                            ->preload()
                            ->required(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Kelompok', 'Konseling Kelompok']))
                            ->visible(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Kelompok', 'Konseling Kelompok']))
                            ->columnSpanFull(), 

                        Forms\Components\Select::make('student_id')
                            ->label('Siswa / Konseli')
                            ->relationship(
                                'student', 
                                'name',
                                function (Builder $query) {
                                    $user = Auth::user();
                                    $query->where('tenant_id', Filament::getTenant()->id);

                                    if ($user->role === 'konselor') {
                                        $classroomIds = $user->classrooms()->pluck('classrooms.id')->toArray();
                                        $query->whereIn('classroom_id', $classroomIds);
                                    }
                                    
                                    $query->orderBy('name', 'asc');
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->class_name} (NIS: {$record->nis})")
                            ->searchable(['name', 'nis', 'nisn'])
                            ->preload()
                            ->required(fn (Get $get) => !in_array($get('service_type'), ['Bimbingan Klasikal', 'Bimbingan Kelompok', 'Konseling Kelompok']))
                            ->visible(fn (Get $get) => !in_array($get('service_type'), ['Bimbingan Klasikal', 'Bimbingan Kelompok', 'Konseling Kelompok']))
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('parties_involved')
                            ->label('Pihak Terkait (Ortu/Wali/Guru/Lembaga)')
                            ->placeholder('Contoh: Ibu Kandung, Wali Kelas, Pihak Kepolisian')
                            ->visible(fn (Get $get) => in_array($get('service_type'), ['Kunjungan Rumah', 'Konferensi Kasus', 'Alih Tangan Kasus', 'Advokasi', 'Kolaborasi', 'Konsultasi']))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Dasar')
                            ->schema([
                                Forms\Components\Select::make('service_type')
                                    ->label('Jenis Layanan (POP BK)')
                                    ->options([
                                        'Konseling Individu' => 'Konseling Individu',
                                        'Bimbingan Klasikal' => 'Bimbingan Klasikal',
                                        'Bimbingan Kelompok' => 'Bimbingan Kelompok',
                                        'Konseling Kelompok' => 'Konseling Kelompok',
                                        'Kunjungan Rumah' => 'Kunjungan Rumah',
                                        'Konferensi Kasus' => 'Konferensi Kasus',
                                        'Advokasi' => 'Advokasi',
                                        'Bimbingan Belajar' => 'Bimbingan Belajar',
                                        'Bimbingan Karir' => 'Bimbingan Karir',
                                        'Bimbingan Pribadi' => 'Bimbingan Pribadi',
                                        'Bimbingan Sosial' => 'Bimbingan Sosial',
                                        'Alih Tangan Kasus' => 'Alih Tangan Kasus',
                                        'Konsultasi' => 'Konsultasi',
                                        'Kolaborasi' => 'Kolaborasi',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if (in_array($state, ['Bimbingan Klasikal', 'Bimbingan Kelompok', 'Konseling Kelompok'])) {
                                            $set('student_id', null);
                                        }
                                    })
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('occurred_at')
                                    ->label('Waktu Pelaksanaan')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Menunggu (Pending)',
                                        'ongoing' => 'Terjadwal / Proses',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->default('ongoing')
                                    ->required()
                                    ->native(false),
                                
                                Forms\Components\Hidden::make('counselor_id')
                                    ->default(fn () => Auth::id()),
                            ])->columns(1),
                    ])->columnSpan(['lg' => 1]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Detail Instrumen POP BK')
                            ->schema([
                                Forms\Components\TextInput::make('topic')
                                    ->label(fn (Get $get) => match ($get('service_type')) {
                                        'Bimbingan Klasikal' => 'Topik Materi Layanan',
                                        'Konseling Individu' => 'Inti Masalah / Gejala',
                                        'Advokasi' => 'Permasalahan Hak Siswa',
                                        default => 'Topik / Masalah'
                                    })
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('method')
                                    ->label('Metode Layanan')
                                    ->placeholder('Ex: Diskusi, Curah Pendapat, Sosiodrama')
                                    ->visible(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Klasikal', 'Bimbingan Kelompok', 'Konseling Kelompok'])),

                                Forms\Components\TextInput::make('media')
                                    ->label('Media / Alat')
                                    ->placeholder('Ex: LCD, LKPD, Video')
                                    ->visible(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Klasikal', 'Bimbingan Kelompok'])),

                                Forms\Components\Select::make('approach')
                                    ->label('Pendekatan Konseling')
                                    ->options([
                                        'Behavioristik' => 'Behavioristik (Behavioral)',
                                        'Client Centered' => 'Berpusat pada Pribadi (Client Centered)',
                                        'REBT' => 'REBT (Rational Emotive Behavior Therapy)',
                                        'Realitas' => 'Terapi Realitas (Reality Therapy)',
                                        'SFBT' => 'SFBT (Solution Focused Brief Therapy)',
                                        'Trait and Factor' => 'Trait and Factor',
                                        'Gestalt' => 'Gestalt',
                                        'Psikoanalisis' => 'Psikoanalisis',
                                        'Eklektik' => 'Eklektik (Integratif)',
                                    ])
                                    ->searchable()
                                    ->visible(fn (Get $get) => $get('service_type') === 'Konseling Individu'),

                                Forms\Components\Textarea::make('location_description')
                                    ->label('Deskripsi Kondisi Rumah/Lingkungan')
                                    ->visible(fn (Get $get) => $get('service_type') === 'Kunjungan Rumah'),

                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi Proses / Verbatim Singkat')
                                    ->rows(3),
                                
                                Forms\Components\Textarea::make('process_evaluation')
                                    ->label('Evaluasi Proses (Observasi)')
                                    ->placeholder('Bagaimana antusiasme peserta didik?')
                                    ->visible(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Klasikal', 'Bimbingan Kelompok', 'Konseling Kelompok'])),

                                Forms\Components\Textarea::make('result')
                                    ->label(fn (Get $get) => in_array($get('service_type'), ['Bimbingan Klasikal']) ? 'Evaluasi Hasil (Laiseg)' : 'Hasil / Tindak Lanjut')
                                    ->rows(3),
                            ]),
                    ])->columnSpan(['lg' => 2]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sasaran_display')
                    ->label('Sasaran')
                    ->state(fn (CounselingService $record) => match ($record->service_type) {
                        'Bimbingan Klasikal' => $record->target_class,
                        'Bimbingan Kelompok', 'Konseling Kelompok' => 'Kelompok (' . $record->students()->count() . ' Siswa)',
                        default => $record->student?->name
                    })
                    ->description(fn (CounselingService $record) => match ($record->service_type) {
                        'Bimbingan Klasikal' => 'Klasikal',
                        'Bimbingan Kelompok', 'Konseling Kelompok' => $record->students->take(3)->pluck('name')->join(', ') . ($record->students->count() > 3 ? ', dst' : ''),
                        default => 'NISN: ' . $record->student?->nisn
                    })
                    ->wrap() 
                    ->sortable(false),

                Tables\Columns\TextColumn::make('service_type')
                    ->label('Layanan')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('counselor.name')
                    ->label('Konselor')
                    ->icon('heroicon-m-user')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'ongoing' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Masuk (Pending)',
                        'ongoing' => 'Terjadwal',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                        default => $state,
                    }),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn (CounselingService $record) => 
                        Auth::user()->role === 'admin_sekolah' || 
                        Auth::user()->role === 'super_admin' ||
                        $record->counselor_id === Auth::id() 
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (CounselingService $record) => 
                        Auth::user()->role === 'admin_sekolah' || 
                        Auth::user()->role === 'super_admin' ||
                        $record->counselor_id === Auth::id() 
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->role === 'admin_sekolah' || Auth::user()->role === 'super_admin'),
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
            'index' => Pages\ListCounselingServices::route('/'),
            'create' => Pages\CreateCounselingService::route('/create'),
            'edit' => Pages\EditCounselingService::route('/{record}/edit'),
        ];
    }
}