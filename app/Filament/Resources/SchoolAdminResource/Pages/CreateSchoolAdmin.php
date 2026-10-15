<?php

namespace App\Filament\Resources\SchoolAdminResource\Pages;

use App\Filament\Resources\SchoolAdminResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolAdmin extends CreateRecord
{
    protected static string $resource = SchoolAdminResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}