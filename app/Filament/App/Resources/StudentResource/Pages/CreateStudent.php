<?php

namespace App\Filament\App\Resources\StudentResource\Pages;

use App\Filament\App\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        if (! Filament::getTenant()->hasStudentQuota()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Kuota siswa untuk sekolah ini sudah penuh. Silakan upgrade paket langganan.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}