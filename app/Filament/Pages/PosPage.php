<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.pages.pos-page';
    protected static ?string $navigationGroup = 'Payment';
    protected static ?string $title = 'Transactions';

    protected static ?string $slug = 'pos-page';
}
