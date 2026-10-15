<?php

namespace App\Filament\Resources\SchoolAdminResource\Pages;

use App\Filament\Resources\SchoolAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchoolAdmins extends ListRecords
{
    protected static string $resource = SchoolAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Admin Baru'),
        ];
    }
}