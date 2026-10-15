<?php

namespace App\Filament\App\Resources\StudentMessageResource\Pages;

use App\Filament\App\Resources\StudentMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentMessages extends ListRecords
{
    protected static string $resource = StudentMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
