<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RegisteredSchoolsChart extends ChartWidget
{
    protected static ?string $heading = 'Registrasi Tahun Ini';    
    protected static ?int $sort = 3;    
    protected int | string | array $columnSpan = 'full';    
    protected static ?string $maxHeight = '300px';
    protected function getData(): array
    {
        $start = now()->startOfYear();
        $end = now()->endOfMonth();

        $results = Tenant::whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_year, count(*) as total")
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->pluck('total', 'month_year')
            ->toArray();

        $data = [];
        $labels = [];

        $current = $start->copy();
        while ($current <= $end) {
            $key = $current->format('Y-m');
            
            $labels[] = $current->translatedFormat('M'); 
            $data[] = $results[$key] ?? 0;
            
            $current->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sekolah Terdaftar',
                    'data' => $data,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}