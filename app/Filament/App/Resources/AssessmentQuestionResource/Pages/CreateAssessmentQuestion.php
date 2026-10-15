<?php

namespace App\Filament\App\Resources\AssessmentQuestionResource\Pages;

use App\Filament\App\Resources\AssessmentQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessmentQuestion extends CreateRecord
{
    protected static string $resource = AssessmentQuestionResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}