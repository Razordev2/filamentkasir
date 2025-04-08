<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class StastikPengeluaran extends ChartWidget
{
    protected static ?string $heading = '📉 Statistik Expenditure';
    protected static ?int $sort = 999;
    protected static string $maxWidth = 'full';

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(fn ($i) => Carbon::now()->subMonths($i)->startOfMonth())->reverse();

        $labels = [];
        $values = [];

        foreach ($months as $month) {
            $start = $month->copy();
            $end = $month->copy()->endOfMonth();

            $total = Pengeluaran::whereBetween('created_at', [$start, $end])->sum('amount');
            $labels[] = $month->format('F Y');
            $values[] = round($total / 1000);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran (Ribuan)',
                    'data' => $values,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 6,
                    'barThickness' => 30,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): ?array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => ['display' => true, 'text' => 'Jumlah (Ribu Rupiah)'],
                    'ticks' => ['stepSize' => 10],
                ],
                'x' => [
                    'title' => ['display' => true, 'text' => 'Bulan'],
                    'ticks' => ['maxRotation' => 45, 'minRotation' => 45],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => \Illuminate\Support\Js::from("function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID') + ' ribu';
                        }"),
                    ],
                ],
            ],
        ];
    }
}
