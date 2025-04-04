<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Pengeluaran;
use App\Models\Discount;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class StatsOverview extends BaseWidget
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth();
        $this->endDate = Carbon::now()->endOfMonth();
    }

    protected function getStats(): array
    {
        $productCount = Product::count();
        $orderCount = Order::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
        $revenue = Order::whereBetween('created_at', [$this->startDate, $this->endDate])->sum('total_price');
        $expenses = $revenue * 0.5;
        
    
        return [             
            Stat::make('📦 Total Produk', Product::count())
            ->description('Jumlah seluruh produk')
            ->descriptionIcon('heroicon-o-cube')
            ->color('success'),

            Stat::make('🔥 Produk Terjual', Order::count())
            ->description('Total produk yang telah terjual')
            ->descriptionIcon('heroicon-o-fire')
            ->color('primary'), 

            Stat::make('💸 Omset', 'Rp ' .  number_format(Order::sum('total_price'), 0, ',', '.'))->description('Total Pemasukan')
                ->descriptionIcon('heroicon-o-chevron-double-up')
                ->color('success'),

            Stat::make('📥​Pengeluaran', 'Rp ' . number_format(Pengeluaran::sum('amount'), 0, ',', '.'))
            ->description('Total Pengeluaran')
            ->descriptionIcon('heroicon-o-chevron-double-down')
            ->color('primary'),     
        ];
        
    }
}
