<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RevenueStats extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalRevenue = Transaction::where('status', 'paid')->sum('amount');
        
        $countPaid = Transaction::where('status', 'paid')->count();

        $revenueThisMonth = Transaction::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
            
        $revenueLastMonth = Transaction::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $diff = $revenueThisMonth - $revenueLastMonth;
        $trendColor = $diff >= 0 ? 'success' : 'danger';
        $trendIcon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total uang masuk')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Transaksi Berhasil', $countPaid . ' Transaksi')
                ->description('Jumlah sekolah membayar')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description($diff >= 0 ? 'Naik dari bulan lalu' : 'Turun dari bulan lalu')
                ->descriptionIcon($trendIcon)
                ->color($trendColor)
                ->chart([$revenueLastMonth, $revenueThisMonth]),
        ];
    }
}