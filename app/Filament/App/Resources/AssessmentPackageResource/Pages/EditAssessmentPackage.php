<?php

namespace App\Filament\App\Resources\AssessmentPackageResource\Pages;

use App\Filament\App\Resources\AssessmentPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentPackage extends EditRecord
{
    protected static string $resource = AssessmentPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
