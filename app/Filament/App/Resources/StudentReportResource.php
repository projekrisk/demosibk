<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentReportResource\Pages;
use App\Models\Student;
use App\Models\StudentReport;
use App\Models\StudentAssessment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class StudentReportResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $modelLabel = 'Rapor BK';
    protected static ?string $pluralModelLabel = 'Rapor BK';
    protected static string | \UnitEnum | null $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'rapor-bk';

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getTenant()->canPrintReport();
    }

    public static function canViewAny(): bool
    {
        return Filament::getTenant()->canPrintReport();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('tenant_id', Filament::getTenant()->id)
            ->where('status', 'Aktif');

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
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_name')
                    ->label('Filter Kelas')
                    ->options(fn () => Student::query()->where('tenant_id', Filament::getTenant()->id)->distinct()->pluck('class_name', 'class_name')->toArray()),
            ])
            ->actions([
                // AKSI EDIT MANUAL
                Tables\Actions\Action::make('fill_report')
                    ->label('Edit/Isi Manual')
                    ->icon('heroicon-m-pencil-square')
                    ->color('gray')
                    ->modalHeading('Edit Rapor BK')
                    ->modalWidth('4xl')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('academic_year')
                                    ->label('Tahun Ajaran')
                                    ->default(fn() => Filament::getTenant()->current_academic_year ?? now()->year . '/' . (now()->year + 1))
                                    ->required(),
                                
                                Forms\Components\Select::make('semester')
                                    ->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])
                                    ->default(Filament::getTenant()->current_semester ?? 'Ganjil')
                                    ->required(),
                            ]),
                        
                        Forms\Components\Section::make('Data Pendukung (Otomatis)')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\TextInput::make('assessment_pribadi')->label('Pribadi (%)')->suffix('%')->numeric()->readOnly(),
                                        Forms\Components\TextInput::make('assessment_sosial')->label('Sosial (%)')->suffix('%')->numeric()->readOnly(),
                                        Forms\Components\TextInput::make('assessment_belajar')->label('Belajar (%)')->suffix('%')->numeric()->readOnly(),
                                        Forms\Components\TextInput::make('assessment_karir')->label('Karir (%)')->suffix('%')->numeric()->readOnly(),
                                    ]),
                                
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('total_achievements')->label('Jml Prestasi')->numeric()->readOnly(),
                                        Forms\Components\TextInput::make('total_violations')->label('Jml Pelanggaran')->numeric()->readOnly(),
                                        Forms\Components\TextInput::make('total_counseling')->label('Jml Layanan')->numeric()->readOnly(),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed(),
                        
                        Forms\Components\Section::make('Analisis Perkembangan')
                            ->schema([
                                Forms\Components\Textarea::make('personality_development')->label('1. Perkembangan Pribadi & Emosi')->rows(3),
                                Forms\Components\Textarea::make('social_development')->label('2. Perkembangan Sosial')->rows(3),
                                Forms\Components\Textarea::make('academic_development')->label('3. Perkembangan Belajar & Karir')->rows(3),
                                Forms\Components\Textarea::make('general_conclusion')->label('4. Kesimpulan & Rekomendasi')->rows(3)->required(),
                            ]),
                    ])
                    ->mountUsing(function (Forms\ComponentContainer $form, Student $record) {
                        $report = StudentReport::where('student_id', $record->id)
                            ->where('academic_year', Filament::getTenant()->current_academic_year ?? now()->year . '/' . (now()->year + 1))
                            ->where('semester', Filament::getTenant()->current_semester ?? 'Ganjil')
                            ->first();

                        if ($report) {
                            $form->fill($report->toArray());
                        } else {
                            $assessment = StudentAssessment::where('student_id', $record->id)->where('status', 'completed')
                                ->whereHas('package', fn ($q) => $q->where('usage_type', 'evaluation'))->latest('filled_at')->first()
                                ?? StudentAssessment::where('student_id', $record->id)->where('status', 'completed')->latest('filled_at')->first();

                            $form->fill([
                                'academic_year' => Filament::getTenant()->current_academic_year,
                                'semester' => Filament::getTenant()->current_semester ?? 'Ganjil',
                                'assessment_pribadi' => $assessment ? $assessment->percentage_pribadi : 0,
                                'assessment_sosial' => $assessment ? $assessment->percentage_sosial : 0,
                                'assessment_belajar' => $assessment ? $assessment->percentage_belajar : 0,
                                'assessment_karir' => $assessment ? $assessment->percentage_karir : 0,
                             ]);
                         }
                     })
                     ->action(function (array $data, Student $record) {
                          $counselorId = Auth::id();
                          
                          // Jika yang edit bukan konselor (misal admin), cari konselor asli kelasnya
                          if (Auth::user()->role !== 'konselor') {
                              $assigned = $record->classroom?->counselors()->first();
                              if ($assigned) {
                                  $counselorId = $assigned->id;
                              }
                          }
 
                          StudentReport::updateOrCreate(
                              [
                                  'tenant_id' => $record->tenant_id,
                                  'student_id' => $record->id,
                                  'academic_year' => $data['academic_year'],
                                  'semester' => $data['semester'],
                              ],
                              [
                                  'counselor_id' => $counselorId,
                                  'personality_development' => $data['personality_development'],
                                  'social_development' => $data['social_development'],
                                  'academic_development' => $data['academic_development'],
                                  'general_conclusion' => $data['general_conclusion'],
                              ]
                          );
                          Notification::make()->title('Perubahan disimpan')->success()->send();
                     }),

                // AKSI CETAK RAPOR
                Tables\Actions\Action::make('print_report')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('academic_year')->label('Tahun Ajaran')->default(fn() => Filament::getTenant()->current_academic_year ?? now()->year . '/' . (now()->year + 1))->required(),
                            Forms\Components\Select::make('semester')->options(['Ganjil' => 'Ganjil', 'Genap' => 'Genap'])->default(fn() => Filament::getTenant()->current_semester ?? 'Ganjil')->required(),
                            Forms\Components\TextInput::make('signing_place')->label('Tempat (Kota)')->default(function() {
                                $address = Filament::getTenant()->address;
                                if($address) { $parts = explode(',', $address); return preg_replace('/[0-9]+/', '', trim(end($parts))); }
                                return '';
                            }),
                            Forms\Components\DatePicker::make('signing_date')->label('Tanggal Tanda Tangan')->default(now())->required(),
                        ])
                    ])
                    ->action(function (array $data, Student $record) {
                        $report = StudentReport::where('student_id', $record->id)->where('academic_year', $data['academic_year'])->where('semester', $data['semester'])->first();
                        if (!$report) {
                            Notification::make()->title('Rapor belum dibuat')->danger()->send(); return;
                        }
                        $tenant = Filament::getTenant();
                        $pdf = Pdf::loadView('pdf.rapor-bk', [
                            'report' => $report, 'student' => $record, 'tenant' => $tenant, 'records' => $record->records, 
                            'signing_place' => $data['signing_place'], 'signing_date' => $data['signing_date'],
                        ])->setPaper([0, 0, 609.45, 935.43], 'portrait');
                        return response()->streamDownload(fn () => print($pdf->output()), 'Rapor-BK-' . $record->nisn . '.pdf');
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentReports::route('/'),
        ];
    }
    
    public static function canCreate(): bool { return false; }
}