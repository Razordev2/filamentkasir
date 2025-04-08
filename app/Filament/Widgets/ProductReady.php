<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class ProductReady extends BaseWidget
{
    protected static ?string $heading = '🛍️ Product Ready';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'height';

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('images')->label('Images')->circular()
            ->size(40),
            TextColumn::make('name')->label('Product name')->sortable()->searchable(),
            TextColumn::make('stock')->label('Stok')->sortable()->badge(),
            TextColumn::make('price')->label('Price')->sortable()
                ->getStateUsing(fn ($record) => 'Rp ' . number_format($record->price, 0, ',', '.')),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()
            ->where('stock', '>', 0)
            ->latest();
    }
}
