<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Orders';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Info Utama')->schema([
                            Forms\Components\TextInput::make('customer_name')->required(),
                            Forms\Components\TextInput::make('customer_email')->email(),
                            Forms\Components\TextInput::make('customer_phone'),
                            Forms\Components\Textarea::make('notes'),
                        ]),
                    ]),
                Section::make('Info Pesanan')->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Produk')
                        ->options(Product::where('stock', '>', 0)->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $product = Product::find($state);
                            if ($product) {
                                if ($product->stock <= 0) {
                                    $set('product_id', null);
                                    Notification::make()
                                        ->title('Produk Habis')
                                        ->body('Produk ini sudah habis dan tidak bisa dipilih.')
                                        ->danger()
                                        ->send();
                                } else {
                                    $discount = $product->discount;
                                    $discountedPrice = $discount ? $discount->getDiscountedPrice($product->price) : $product->price;
                                    $set('unit_price', $product->price);
                                    $set('discounted_price', $discountedPrice);
                                    $set('total_price', $discountedPrice);
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('unit_price')->numeric()->required()->disabled(),
                    Forms\Components\TextInput::make('discounted_price')->numeric()->required()->disabled(),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $get, callable $set) => 
                            $set('total_price', $get('discounted_price') * $state)
                        ),
                ]),
                Section::make('Pembayaran')->schema([
                    Forms\Components\Select::make('payment_method_id')
                        ->label('Metode Pembayaran')
                        ->options(PaymentMethod::all()->pluck('paymentmethods', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('total_price')
                        ->numeric()
                        ->required()
                ]),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')->label('Pelanggan')->weight('bold'),
                Tables\Columns\TextColumn::make('product.name')->label('Produk'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah')->badge(),
                Tables\Columns\TextColumn::make('paymentMethod.paymentmethods')->label('Metode Pembayaran')->badge(),
                Tables\Columns\TextColumn::make('total_price')->label('Total Harga')->money('IDR'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('confirm')
                        ->label('Konfirmasi')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($record) => $record->update(['status' => 'confirmed']))
                        ->visible(fn ($record) => $record->status !== 'confirmed'),
                ])->tooltip('Actions'),
            ])            
            ->filters([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
