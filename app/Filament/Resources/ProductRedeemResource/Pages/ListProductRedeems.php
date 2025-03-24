<?php

namespace App\Filament\Resources\ProductRedeemResource\Pages;

use App\Filament\Resources\ProductRedeemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductRedeems extends ListRecords
{
    protected static string $resource = ProductRedeemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
