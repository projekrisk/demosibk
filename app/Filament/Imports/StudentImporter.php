<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            
            ImportColumn::make('nisn')
                ->label('NISN')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            
            ImportColumn::make('name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            
            ImportColumn::make('class_name')
                ->label('Kelas')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            
            ImportColumn::make('status')
                ->label('Status')
                ->rules(['max:255']),
            
            ImportColumn::make('points')
                ->label('Poin')
                ->numeric()
                ->rules(['integer', 'min:0']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        return Student::firstOrNew([
            'nisn' => $this->data['nisn'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data siswa telah selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal karena validasi.';
        }

        return $body;
    }
}