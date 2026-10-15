<?php

namespace App\Filament\App\Widgets;

use App\Models\CounselingService;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class ServiceTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Jenis Layanan';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        $data = CounselingService::where('tenant_id', $tenant->id)
            ->selectRaw('service_type, count(*) as total')
            ->groupBy('service_type')
            ->pluck('total', 'service_type')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Layanan',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', 
                        '#8b5cf6', '#ef4444', '#14b8a6', '#f97316', '#64748b'
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}