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

class ProductFavorite extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'height'; 


    protected function getTableQuery(): Builder|Relation|null
    {
        return Product::query()->withCount('productRedeems');
    }
    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('images')->label('Images'),
            TextColumn::make('name')->label('Nama Produk')->sortable()->searchable(),
            TextColumn::make('stock')->label('Stok')->sortable(),
            TextColumn::make('price')->label('Harga')
                ->getStateUsing(fn ($record) => 'Rp ' . number_format($record->price, 0, ',', '.')),
        ];
    }
}