<?php

namespace App\Filament\App\Resources\LevelResource\Pages;

use App\Filament\App\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLevel extends EditRecord
{
    protected static string $resource = LevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
