<?php

namespace App\Filament\App\Resources\AssessmentQuestionResource\Pages;

use App\Filament\App\Resources\AssessmentQuestionResource;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentQuestion extends EditRecord
{
    protected static string $resource = AssessmentQuestionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}