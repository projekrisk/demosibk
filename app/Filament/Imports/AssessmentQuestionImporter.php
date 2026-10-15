<?php

namespace App\Filament\Imports;

use App\Models\AssessmentQuestion;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Facades\Filament;

class AssessmentQuestionImporter extends Importer
{
    protected static ?string $model = AssessmentQuestion::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category')
                ->label('Bidang (Pribadi/Sosial/Belajar/Karir)')
                ->requiredMapping()
                ->rules(['required', 'string']),

            ImportColumn::make('question_text')
                ->label('Pernyataan Masalah')
                ->requiredMapping()
                ->rules(['required', 'string']),

            ImportColumn::make('sort_order')
                ->label('No Urut')
                ->numeric()
                ->rules(['integer']),
        ];
    }

    public function resolveRecord(): ?AssessmentQuestion
    {
        return new AssessmentQuestion([
            'tenant_id' => Filament::getTenant()->id,
            'is_active' => true,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Impor bank soal selesai. ' . number_format($import->successful_rows) . ' butir pernyataan berhasil ditambahkan.';
    }
}