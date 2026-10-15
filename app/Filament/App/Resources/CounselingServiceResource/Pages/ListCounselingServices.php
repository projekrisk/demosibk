<?php

namespace App\Filament\App\Resources\CounselingServiceResource\Pages;

use App\Filament\App\Resources\CounselingServiceResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCounselingServices extends ListRecords
{
    protected static string $resource = CounselingServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Data')
                ->badge($this->getResource()::getEloquentQuery()->count()),
            
            'masuk' => Tab::make('Masuk (Inbox)')
                ->icon('heroicon-m-inbox')
                ->badge($this->getResource()::getEloquentQuery()->where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'terjadwal' => Tab::make('Terjadwal')
                ->icon('heroicon-m-calendar')
                ->badge($this->getResource()::getEloquentQuery()->where('status', 'ongoing')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ongoing')),

            'selesai' => Tab::make('Selesai')
                ->icon('heroicon-m-check-circle')
                ->badge($this->getResource()::getEloquentQuery()->where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
        ];
    }
}