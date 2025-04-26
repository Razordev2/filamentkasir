<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryLogsResource\Pages;
use App\Models\InventoryLogs;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryLogsResource extends Resource
{
    protected static ?string $model = InventoryLogs::class;
    protected static ?string $label = 'Tambah Barang';
    protected static ?String $navigationGroup = 'DATA MASTER';
protected static ?string $navigationIcon = 'heroicon-o-arrows-pointing-out';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $product = Product::find($state);
                        $set('stock', $product?->stock ?? 0);
                    }),
                
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Date')
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('stock')
                    ->label('Current Stock')
                    ->hidden()
                    ->dehydrated(false), 
                   
                Forms\Components\TextInput::make('stock_change')
                    ->label('Stock Change')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryLogs::route('/'),
            'create' => Pages\CreateInventoryLogs::route('/create'),
            'edit' => Pages\EditInventoryLogs::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
