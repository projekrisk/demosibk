<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Tenant::count();

        $newThisMonth = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $expired = Tenant::whereIn('subscription_status', ['expired', 'inactive'])->count();

        return [
            Stat::make('Total Sekolah', $total)
                ->description('Total klien terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 10, 5, 15, 10, 20]),

            Stat::make('Pertumbuhan Bulan Ini', $newThisMonth)
                ->description('Sekolah baru bergabung')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Perlu Follow-up', $expired)
                ->description('Status Expired/Inactive')
                ->descriptionIcon('heroicon-m-phone')
                ->color('danger'),
        ];
    }
}