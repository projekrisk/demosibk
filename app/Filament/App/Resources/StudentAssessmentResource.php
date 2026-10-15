<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StudentAssessmentResource\Pages;
use App\Models\StudentAssessment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Width;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection; // Import Collection
use Filament\Notifications\Notification; // Import Notification

class StudentAssessmentResource extends Resource
{
    protected static ?string $model = StudentAssessment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $modelLabel = 'Hasil Asesmen';
    protected static ?string $pluralModelLabel = 'Hasil Asesmen Siswa';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Asesmen Kebutuhan';
    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'hasil-asesmen';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('tenant_id', Filament::getTenant()->id);
        
        $user = Auth::user();
        if ($user && $user->role === 'konselor') {
             $query->whereHas('student', function ($q) use ($user) {
                 $q->whereHas('classroom', function ($q2) use ($user) {
                     $q2->whereHas('counselors', function ($q3) use ($user) {
                         $q3->where('users.id', $user->id);
                     });
                 });
             });
        }
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
             Forms\Components\Section::make('Identitas Pengisian')
                ->schema([
                    Forms\Components\Select::make('student_id')
                        ->label('Nama Siswa')
                        ->relationship('student', 'name')
                        ->disabled(), 
                    Forms\Components\DatePicker::make('filled_at')
                        ->label('Tanggal')
                        ->disabled(),
                ])->columns(2),
             
             Forms\Components\Section::make('Hasil')
                ->schema([
                    Forms\Components\TextInput::make('percentage_pribadi')->label('Pribadi')->suffix('%'),
                    Forms\Components\TextInput::make('percentage_sosial')->label('Sosial')->suffix('%'),
                    Forms\Components\TextInput::make('percentage_belajar')->label('Belajar')->suffix('%'),
                    Forms\Components\TextInput::make('percentage_karir')->label('Karir')->suffix('%'),
                    Forms\Components\Textarea::make('counselor_notes')->label('Catatan Konselor')->rows(3)->columnSpanFull(),
                ])->columns(4)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('filled_at')->label('Tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('student.name')->label('Siswa')->searchable()->weight('bold')->description(fn ($record) => $record->student->class_name ?? '-'),
                Tables\Columns\TextColumn::make('package.title')->label('Paket')->limit(20),
                
                // KOLOM RIASEC
                Tables\Columns\TextColumn::make('riasec_result')
                    ->label('Rekomendasi Jurusan')
                    ->badge()
                    ->color('primary')
                    ->state(fn (StudentAssessment $record) => $record->riasec_result ? $record->riasec_result['recommendation'] : null)
                    ->description(fn (StudentAssessment $record) => $record->riasec_result ? 'Kode Holland: ' . $record->riasec_result['code'] : null)
                    ->visible(fn () => true),
                
                // KOLOM AKPD LAMA
                Tables\Columns\TextColumn::make('percentage_pribadi')->label('Pribadi')->suffix('%')->alignCenter()->color(fn ($state) => $state > 50 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('percentage_sosial')->label('Sosial')->suffix('%')->alignCenter()->color(fn ($state) => $state > 50 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('percentage_belajar')->label('Belajar')->suffix('%')->alignCenter()->color(fn ($state) => $state > 50 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('percentage_karir')->label('Karir')->suffix('%')->alignCenter()->color(fn ($state) => $state > 50 ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assessment_package_id')
                    ->label('Filter Paket')
                    // PERBAIKAN: Gunakan \Illuminate\Database\Eloquent\Builder secara eksplisit
                    ->relationship('package', 'title', fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('tenant_id', Filament::getTenant()->id)),
            ])
            ->headerActions([
                // Tombol cetak rekap umum dihapus karena sudah digantikan oleh tombol
                // "Cetak Rekap Kelas" yang lebih spesifik di halaman ListStudentAssessments.php
            ])
            ->actions([
                Tables\Actions\Action::make('print_individual')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (StudentAssessment $record) {
                        $tenant = Filament::getTenant();
                        $pdf = Pdf::loadView('pdf.student-assessment-detail', [
                            'assessment' => $record,
                            'student' => $record->student,
                            'package' => $record->package,
                            'answers' => $record->answers()->with('question')->get(),
                            'tenant' => $tenant
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'Hasil-Asesmen-' . $record->student->name . '.pdf');
                    }),
                Tables\Actions\ViewAction::make()->label('Detail Jawaban')->icon('heroicon-m-eye')->modalWidth(Width::SevenExtraLarge)->slideOver(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    // FITUR BARU: HITUNG ULANG SKOR (RECALCULATE)
                    Tables\Actions\BulkAction::make('recalculate_scores')
                        ->label('Hitung Ulang Skor (Perbaiki 0%)')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Hitung Ulang Nilai Asesmen')
                        ->modalDescription('Tindakan ini akan membaca ulang semua rincian jawaban siswa dan menghitung kembali persentase/skor RIASEC mereka. Gunakan ini jika ada siswa yang nilainya masih 0 atau terjadi perubahan rumus.')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $assessment) {
                                $usageType = $assessment->package->usage_type ?? 'diagnostic';
                                $answers = $assessment->answers()->with('question')->get();

                                // JIKA PAKET ADALAH RIASEC
                                if ($usageType === 'riasec') {
                                    $riasecScores = ['r' => 0, 'i' => 0, 'a' => 0, 's' => 0, 'e' => 0, 'c' => 0];
                                    
                                    foreach ($answers as $ans) {
                                        if ($ans->question) {
                                            $cat = strtolower(trim($ans->question->category)); 
                                            if (array_key_exists($cat, $riasecScores)) {
                                                $val = 0;
                                                if (is_numeric($ans->answer_value)) {
                                                    $val = (int) $ans->answer_value;
                                                } elseif ($ans->answer_value === 'true' || strtolower($ans->answer_value) === 'ya' || $ans->answer_value === '1') {
                                                    $val = 1;
                                                }
                                                $riasecScores[$cat] += $val;
                                            }
                                        }
                                    }

                                    $assessment->update([
                                        'score_r' => $riasecScores['r'],
                                        'score_i' => $riasecScores['i'],
                                        'score_a' => $riasecScores['a'],
                                        'score_s' => $riasecScores['s'],
                                        'score_e' => $riasecScores['e'],
                                        'score_c' => $riasecScores['c'],
                                    ]);
                                } 
                                // JIKA PAKET ADALAH AKPD (Masalah)
                                else {
                                    $scores = [
                                        'pribadi' => ['total' => 0, 'max_score' => 0],
                                        'sosial'  => ['total' => 0, 'max_score' => 0],
                                        'belajar' => ['total' => 0, 'max_score' => 0],
                                        'karir'   => ['total' => 0, 'max_score' => 0],
                                    ];

                                    foreach ($answers as $ans) {
                                        if ($ans->question) {
                                            $cat = strtolower(trim($ans->question->category)); 
                                            $type = $ans->question->type;
                                            
                                            if (array_key_exists($cat, $scores)) {
                                                $val = 0;
                                                if (is_numeric($ans->answer_value)) {
                                                    $val = (int) $ans->answer_value;
                                                } elseif ($ans->answer_value === 'true' || strtolower($ans->answer_value) === 'ya' || $ans->answer_value === '1') {
                                                    $val = 1;
                                                }

                                                $maxScale = 1; 
                                                if ($type === 'likert' || $type === 'scale') {
                                                    $maxScale = 4;
                                                } elseif ($type === 'rating') {
                                                    $maxScale = 10;
                                                }
                                                
                                                if ($type !== 'text' && $type !== 'open') {
                                                    $scores[$cat]['total'] += $val;
                                                    $scores[$cat]['max_score'] += $maxScale;
                                                }
                                            }
                                        }
                                    }

                                    $updateData = [];
                                    foreach ($scores as $cat => $val) {
                                        if ($val['max_score'] > 0) {
                                            $percentage = round(($val['total'] / $val['max_score']) * 100); 
                                            if ($percentage > 100) $percentage = 100;
                                            $updateData['percentage_' . $cat] = $percentage;
                                        } else {
                                             $updateData['percentage_' . $cat] = 0;
                                        }
                                    }

                                    if (!empty($updateData)) {
                                        $assessment->update($updateData);
                                    }
                                }
                                $count++;
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body("Skor {$count} data asesmen berhasil dihitung ulang dan diperbarui!")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Infolists\Components\Section::make('Identitas')
                ->schema([
                    Infolists\Components\TextEntry::make('student.name')->label('Nama'),
                    Infolists\Components\TextEntry::make('student.class_name')->label('Kelas'),
                ])->columns(2),
            Infolists\Components\Section::make('Hasil')
                ->schema([
                    Infolists\Components\TextEntry::make('percentage_pribadi')->label('Pribadi (%)'),
                    Infolists\Components\TextEntry::make('percentage_sosial')->label('Sosial (%)'),
                    Infolists\Components\TextEntry::make('percentage_belajar')->label('Belajar (%)'),
                    Infolists\Components\TextEntry::make('percentage_karir')->label('Karir (%)'),
                ])->columns(4)
        ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAssessments::route('/'),
        ];
    }
}