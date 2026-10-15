<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;

class TopSchoolsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Sekolah (Siswa Terbanyak)';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $schools = Tenant::withCount('students')
            ->orderByDesc('students_count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $schools->pluck('students_count'),
                    'backgroundColor' => '#4f46e5',
                    'borderColor' => '#4f46e5',
                ],
            ],
            'labels' => $schools->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}