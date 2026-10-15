<?php

namespace App\Filament\App\Resources\StudentMessageResource\Pages;

use App\Filament\App\Resources\StudentMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentMessage extends ViewRecord
{
    protected static string $resource = StudentMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}