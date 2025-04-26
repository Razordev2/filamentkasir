<?php

namespace App\Filament\Resources\InventoryLogsResource\Pages;

use App\Filament\Resources\InventoryLogsResource;
use App\Models\Product;
use Filament\Resources\Pages\EditRecord;

class EditInventoryLogs extends EditRecord
{
    protected static string $resource = InventoryLogsResource::class;

    protected function afterSave(): void
    {
        $product = Product::find($this->record->product_id);
        if ($product) {
            $product->stock += $this->record->stock_change;
            $product->save();
        }
    }
}
