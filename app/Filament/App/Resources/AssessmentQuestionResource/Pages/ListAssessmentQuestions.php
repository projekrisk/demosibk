<?php

namespace App\Filament\App\Resources\AssessmentQuestionResource\Pages;

use App\Filament\App\Resources\AssessmentQuestionResource;
use App\Filament\Imports\AssessmentQuestionImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use App\Models\AssessmentQuestion;

class ListAssessmentQuestions extends ListRecords
{
    protected static string $resource = AssessmentQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('downloadTemplate')
                    ->label('Download Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $fileName = 'template_instrumen_bk.xlsx';
                        $filePath = sys_get_temp_dir() . '/' . $fileName;

                        $writer = new Writer();
                        $writer->openToFile($filePath);

                        $headerRow = Row::fromValues(['Bidang', 'Pernyataan Masalah', 'No Urut']);
                        $writer->addRow($headerRow);

                        $writer->addRow(Row::fromValues(['Pribadi', 'Saya sering merasa kurang percaya diri.', '1']));
                        $writer->addRow(Row::fromValues(['Sosial', 'Saya sulit memulai percakapan dengan orang baru.', '2']));
                        $writer->addRow(Row::fromValues(['Belajar', 'Saya kesulitan mengatur waktu belajar di rumah.', '3']));
                        $writer->addRow(Row::fromValues(['Karir', 'Saya belum memiliki gambaran pilihan studi lanjut.', '4']));

                        $writer->close();

                        return response()->download($filePath, $fileName)->deleteFileAfterSend();
                    }),

                Actions\Action::make('import')
                    ->label('Impor Soal (Excel)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih File Excel (.xlsx)')
                            ->disk('public_uploads')
                            ->directory('temp-imports')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = public_path('uploads/' . $data['file']);
                        
                        try {
                            $reader = new Reader();
                            $reader->open($filePath);

                            $successCount = 0;
                            $tenantId = Filament::getTenant()->id;

                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $index => $row) {
                                    if ($index === 1) continue;

                                    $cells = $row->getCells();
                                    if (count($cells) < 2) continue;

                                    $category = $cells[0]->getValue();
                                    $text = $cells[1]->getValue();
                                    $sort = isset($cells[2]) ? $cells[2]->getValue() : 0;

                                    if (!$category || !$text) continue;

                                    $validCategory = match(strtolower(trim($category))) {
                                        'pribadi' => 'Pribadi',
                                        'sosial' => 'Sosial',
                                        'belajar' => 'Belajar',
                                        'karir' => 'Karir',
                                        default => 'Pribadi'
                                    };

                                    AssessmentQuestion::create([
                                        'tenant_id' => $tenantId,
                                        'category' => $validCategory,
                                        'question_text' => $text,
                                        'sort_order' => (int)$sort,
                                        'is_active' => true,
                                    ]);
                                    
                                    $successCount++;
                                }
                            }

                            $reader->close();
                            @unlink($filePath);

                            Notification::make()
                                ->title('Impor Berhasil')
                                ->body("{$successCount} butir pernyataan berhasil ditambahkan ke Bank Soal.")
                                ->success()
                                ->send();

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
            ->button(),

            Actions\CreateAction::make()
                ->label('Buat Baru'),
        ];
    }
}