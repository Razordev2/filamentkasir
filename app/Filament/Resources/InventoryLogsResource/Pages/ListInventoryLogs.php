<?php

namespace App\Filament\Resources\InventoryLogsResource\Pages;

use App\Filament\Resources\InventoryLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryLogs extends ListRecords
{
    protected static string $resource = InventoryLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
