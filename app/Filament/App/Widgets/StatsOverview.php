<?php

namespace App\Filament\App\Widgets;

use App\Models\CounselingService;
use App\Models\DailyJournal;
use App\Models\Student;
use App\Models\StudentMessage;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $user = Auth::user();
        $isKonselor = $user->role === 'konselor';

        $totalSiswa = Student::where('tenant_id', $tenant->id)
            ->where('status', 'Aktif')
            ->count();

        $layananQuery = CounselingService::where('tenant_id', $tenant->id)
            ->whereMonth('occurred_at', now()->month)
            ->whereYear('occurred_at', now()->year);

        if ($isKonselor) {
            $jumlahLayanan = $layananQuery->where('counselor_id', $user->id)->count();
            $labelLayanan = 'Layanan Saya (Bulan Ini)';
            $descLayanan = 'Sesi konseling Anda';
        } else {
            $jumlahLayanan = $layananQuery->count();
            $labelLayanan = 'Total Layanan (Bulan Ini)';
            $descLayanan = 'Akumulasi seluruh konselor';
        }

        $jurnalQuery = DailyJournal::where('tenant_id', $tenant->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        if ($isKonselor) {
            $totalMenit = $jurnalQuery->where('user_id', $user->id)->sum('duration_minutes');
            $totalJam = round($totalMenit / 60, 1);
            
            $statKinerja = Stat::make('Kinerja Jurnal', $totalJam . ' Jam')
                ->description('Total durasi aktivitas bulan ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success');
        } else {
            $totalEntri = $jurnalQuery->count();
            $statKinerja = Stat::make('Total Jurnal Guru', $totalEntri . ' Entri')
                ->description('Aktivitas tercatat bulan ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info');
        }

        $bookingPending = CounselingService::where('tenant_id', $tenant->id)
            ->where('status', 'pending');
            
        if ($isKonselor) {
            $bookingPending->where('counselor_id', $user->id);
        }
        
        $countBooking = $bookingPending->count();
        $countPesan = StudentMessage::where('tenant_id', $tenant->id)->whereNull('replied_at')->count();
        
        $totalNotif = $countBooking + $countPesan;

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->description('Siswa aktif terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('info'),

            Stat::make($labelLayanan, $jumlahLayanan)
                ->description($descLayanan)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary')
                ->chart([2, 5, 3, 7, 5, 8, 10]), 

            $statKinerja, 

            Stat::make('Menunggu Respon', $totalNotif)
                ->description($countBooking . ' Booking, ' . $countPesan . ' Pesan')
                ->descriptionIcon('heroicon-m-bell')
                ->color($totalNotif > 0 ? 'danger' : 'gray'),
        ];
    }
}