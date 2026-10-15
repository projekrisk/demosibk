<?php

namespace App\Filament\App\Resources\CounselingServiceResource\Pages;

use App\Filament\App\Resources\CounselingServiceResource;
use Filament\Resources\Pages\EditRecord;

class EditCounselingService extends EditRecord
{
    protected static string $resource = CounselingServiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}