<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\Carbon;

class StastikOmset extends ChartWidget
{
    protected static ?string $heading = '📊 Statistik Omset';
    protected static ?int $sort = 998;
    protected static string $maxWidth = 'full';

    protected function getData(): array
    {
        $data = [];

        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->startOfMonth();
        })->reverse();

        $labels = [];
        $values = [];

        foreach ($months as $month) {
            $start = $month->copy();
            $end = $month->copy()->endOfMonth();

            $total = Order::whereBetween('created_at', [$start, $end])
                ->sum('total_price');

            $labels[] = $month->format('F Y');
            $values[] = round($total / 1000); 
        }

        return [
            'datasets' => [
                [
                    'label' => 'Omset (Ribuan)',
                    'data' => $values,
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
