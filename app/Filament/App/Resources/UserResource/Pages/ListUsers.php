<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(fn() => Filament::getTenant()->hasCounselorQuota() ? 'Tambah Akun' : 'Kuota Penuh (Upgrade)')
                ->disabled(fn() => !Filament::getTenant()->hasCounselorQuota())
                ->visible(fn () => in_array(Auth::user()->role, ['super_admin', 'admin_sekolah'])),
        ];
    }
}