<?php

namespace App\Filament\App\Resources\StudentRecordResource\Pages;

use App\Filament\App\Resources\StudentRecordResource;
use Filament\Resources\Pages\EditRecord;

class EditStudentRecord extends EditRecord
{
    protected static string $resource = StudentRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}