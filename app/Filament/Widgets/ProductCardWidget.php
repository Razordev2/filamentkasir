<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Discount;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class ProductCardWidget extends BaseWidget
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
        
        $revenue = 1000000; 
        $expenses = 500000;

        return [
            Stat::make('Total Products', $productCount)->color('primary'),
            Stat::make('Total Orders', $orderCount)->color('success'),
            Stat::make('Omset', 'Rp ' . number_format($revenue, 0, ',', '.'))->color('warning'),
            Stat::make('Pengeluaran', 'Rp ' . number_format($expenses, 0, ',', '.'))->color('danger'),
        ];
    }

    public static function canView(): bool 
    {
        return auth()->user()->hasRole('super_admin');
    } 
}