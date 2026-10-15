<?php

namespace App\Filament\App\Resources\StudentRecordResource\Pages;

use App\Filament\App\Resources\StudentRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentRecord extends CreateRecord
{
    protected static string $resource = StudentRecordResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}