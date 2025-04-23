<?php

namespace App\Filament\Widgets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Product;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Discount;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\DB;

class ProductFavorite extends BaseWidget
{
protected static ?string $heading = '⭐ Product Favorite';
protected static ?int $sort = 5;
protected int | string | array $columnSpan = 'height'; 

protected function getTableQuery(): Builder
{
    return Product::query()
        ->join('orders', 'products.id', '=', 'orders.product_id')
        ->selectRaw('products.id, products.name, products.stock, products.price, products.images, SUM(orders.quantity) as total_order')
        ->groupBy('products.id', 'products.name', 'products.stock', 'products.price', 'products.images')
        ->orderByDesc('total_order');
}


protected function getTableColumns(): array
{
    return [
        ImageColumn::make('images')
            ->label('Images')
            ->circular()
            ->size(40),

        TextColumn::make('name')
            ->label('Product name')
            ->sortable()
            ->searchable(),

        TextColumn::make('price')
            ->label('Price')
            ->sortable()
            ->getStateUsing(fn ($record) => 'Rp ' . number_format($record->price, 0, ',', '.')),

        TextColumn::make('total_order')
            ->label('Total Ordered')
            ->sortable()
            ->color('primary')
            ->weight('bold')
            ->badge(),

    ];
}

protected function isTablePaginationEnabled(): bool
{
    return true;
}

protected function getTableRecordsPerPageSelectOptions(): array
{
    return [5, 10, 25, 50, 100];
}
}