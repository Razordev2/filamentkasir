<?php

namespace App\Filament\Resources\ProductRedeemResource\Pages;

use App\Filament\Resources\ProductRedeemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductRedeem extends EditRecord
{
    protected static string $resource = ProductRedeemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
