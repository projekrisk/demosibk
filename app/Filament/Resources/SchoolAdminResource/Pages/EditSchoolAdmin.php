<?php

namespace App\Filament\Resources\SchoolAdminResource\Pages;

use App\Filament\Resources\SchoolAdminResource;
use Filament\Resources\Pages\EditRecord;

class EditSchoolAdmin extends EditRecord
{
    protected static string $resource = SchoolAdminResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}