<?php

namespace App\Filament\App\Resources\DailyJournalResource\Pages;

use App\Filament\App\Resources\DailyJournalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyJournal extends CreateRecord
{
    protected static string $resource = DailyJournalResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}