<?php

namespace App\Filament\App\Resources\CounselingServiceResource\Pages;

use App\Filament\App\Resources\CounselingServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCounselingService extends CreateRecord
{
    protected static string $resource = CounselingServiceResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}