<?php

namespace App\Filament\App\Resources\AssessmentPackageResource\Pages;

use App\Filament\App\Resources\AssessmentPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentPackages extends ListRecords
{
    protected static string $resource = AssessmentPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
