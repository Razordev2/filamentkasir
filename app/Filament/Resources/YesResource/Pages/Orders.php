<?php

namespace App\Filament\Resources\YesResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class Orders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
