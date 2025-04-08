<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::find($data['product_id']);
        if (!$product || $product->stock < $data['quantity']) {
            Notification::make()
                ->title('Stok Tidak Cukup')
                ->body('Jumlah produk melebihi stok yang tersedia.')
                ->danger()
                ->send();

            throw new \Exception('Jumlah melebihi stok yang tersedia.');
        }

        return $data;
    }
}
