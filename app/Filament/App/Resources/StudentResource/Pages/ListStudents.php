<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Level;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use Illuminate\Support\Facades\Auth;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $fileName = 'template_siswa.xlsx';
                        $filePath = sys_get_temp_dir() . '/' . $fileName;

                        $writer = new Writer();
                        $writer->openToFile($filePath);

                        $headerRow = Row::fromValues(['NIS', 'NISN', 'Nama Lengkap', 'Nama Kelas', 'Status']);
                        $writer->addRow($headerRow);

                        $dummyRow = Row::fromValues(['1001', '0012345678', 'Budi Santoso', 'X-E1', 'Aktif']);
                        $writer->addRow($dummyRow);

                        $writer->close();

                        return response()->download($filePath, $fileName)->deleteFileAfterSend();
                    }),

                Actions\Action::make('import')
                    ->label('Data Siswa')
                    ->modalDescription('Jika file .xlsx tidak muncul, ubah filter file di pojok kanan bawah jendela dialog menjadi "All Files".')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih File Excel (.xlsx)')
                            ->disk('public_uploads')
                            ->directory('temp-imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                                'text/plain'
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = public_path('uploads/' . $data['file']);
                        
                        try {
                            $reader = new Reader();
                            $reader->open($filePath);

                            $successCount = 0;
                            $skippedCount = 0; 
                            
                            $tenant = Filament::getTenant();
                            $tenantId = $tenant->id;

                            $limits = $tenant->getLimits();
                            $maxStudents = $limits['students']; 
                            $currentCount = $tenant->students()->count();

                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $index => $row) {
                                    if ($index === 1) continue;

                                    $cells = $row->getCells();
                                    if (count($cells) < 3) continue;

                                    $nis    = $cells[0]->getValue() ?? null;
                                    $nisn   = $cells[1]->getValue() ?? null;
                                    $name   = $cells[2]->getValue() ?? null;
                                    $classNameRaw = $cells[3]->getValue() ?? null;
                                    $status = $cells[4]->getValue() ?? 'Aktif';

                                    if (!$nisn || !$name) continue;

                                    $existingStudent = Student::where('tenant_id', $tenantId)
                                        ->where('nisn', $nisn)
                                        ->first();

                                    if (!$existingStudent && $maxStudents !== null && $currentCount >= $maxStudents) {
                                        $skippedCount++;
                                        continue; 
                                    }

                                    $classroomId = null;
                                    $finalClassName = '-';

                                    if ($classNameRaw) {
                                        $finalClassName = trim((string)$classNameRaw);
                                        
                                        $levelName = '10'; 
                                        
                                        $level = Level::firstOrCreate(
                                            ['tenant_id' => $tenantId, 'name' => $levelName]
                                        );

                                        $classroom = Classroom::firstOrCreate(
                                            ['tenant_id' => $tenantId, 'name' => $finalClassName],
                                            ['level_id' => $level->id]
                                        );
                                        $classroomId = $classroom->id;
                                    }

                                    if ($existingStudent) {
                                        $existingStudent->update([
                                            'nis' => $nis,
                                            'name' => $name,
                                            'classroom_id' => $classroomId,
                                            'class_name' => $finalClassName,
                                            'status' => $status,
                                        ]);
                                    } else {
                                        Student::create([
                                            'tenant_id' => $tenantId,
                                            'nisn' => $nisn, 
                                            'nis' => $nis,
                                            'name' => $name,
                                            'classroom_id' => $classroomId,
                                            'class_name' => $finalClassName,
                                            'status' => $status,
                                        ]);
                                        $currentCount++;
                                    }
                                    
                                    $successCount++;
                                }
                            }

                            $reader->close();
                            @unlink($filePath);

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title('Impor Selesai dengan Peringatan')
                                    ->body("Berhasil: {$successCount}. Ditolak (Kuota Penuh): {$skippedCount}.")
                                    ->warning()
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Impor Berhasil')
                                    ->body("{$successCount} siswa berhasil diproses.")
                                    ->success()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Membaca File')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->label('Menu Import')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('info')
            ->button()
            ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),

            Actions\CreateAction::make()
                ->label(fn() => Filament::getTenant()->hasStudentQuota() ? 'Buat Peserta Didik' : 'Kuota Penuh')
                ->disabled(fn() => !Filament::getTenant()->hasStudentQuota())
                ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),
        ];
    }
}