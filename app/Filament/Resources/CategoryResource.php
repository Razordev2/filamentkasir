<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class CategoryResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Products'; 
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
        ];
    }
}
