<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        if (! Filament::getTenant()->hasCounselorQuota()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Kuota akun konselor untuk sekolah ini sudah penuh. Silakan upgrade paket langganan.')
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

    protected function afterCreate(): void
    {
        $user = $this->record;
        
        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $currentTenant = Filament::getTenant();

        if ($currentTenant) {
            $user->tenants()->attach($currentTenant->id);
        }
    }
}