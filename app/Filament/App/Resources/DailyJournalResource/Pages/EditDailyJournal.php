<?php

namespace App\Filament\App\Resources\DailyJournalResource\Pages;

use App\Filament\App\Resources\DailyJournalResource;
use Filament\Resources\Pages\EditRecord;

class EditDailyJournal extends EditRecord
{
    protected static string $resource = DailyJournalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}