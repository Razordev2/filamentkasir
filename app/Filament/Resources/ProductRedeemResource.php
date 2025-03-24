<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductRedeemResource\Pages;
use App\Models\ProductRedeem;
use App\Models\Member;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class ProductRedeemResource extends Resource
{
    protected static ?string $model = ProductRedeem::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';
    protected static ?string $navigationGroup = 'Management'; 
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('member_id')->label('Member')->options(Member::all()->pluck('name', 'id'))->required(),
                Select::make('product_id')->label('Produk')->options(Product::all()->pluck('name', 'id'))->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')->label('Member')->sortable(),
                TextColumn::make('product.name')->label('Produk')->sortable(),
                TextColumn::make('points_used')->label('Poin Digunakan')->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductRedeems::route('/'),
        ];
    }
}
