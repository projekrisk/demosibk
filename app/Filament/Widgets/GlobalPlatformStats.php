<?php

namespace App\Filament\Widgets;

use App\Models\CounselingService;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalPlatformStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Populasi Siswa', number_format(Student::count()))
                ->description('Terdaftar di seluruh sistem')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 10, 14, 20, 25, 22, 30])
                ->color('info'),

            Stat::make('Sekolah', Tenant::where('subscription_status', 'active')->count() . ' / ' . Tenant::count())
                ->description('Aktif / Terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Stat::make('Aktivitas', number_format(CounselingService::count()))
                ->description('Layanan tercatat')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
        ];
    }
}