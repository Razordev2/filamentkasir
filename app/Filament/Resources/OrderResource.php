<?php

namespace App\Filament\Resources;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentMethod;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Exports\Exports\OrdersExport;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'History Transaction';
    protected static ?string $modelLabel = 'History Transaction';
    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = 'Payment';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Form Checkout')
                ->schema([
                    Forms\Components\TextInput::make('customer_name')->required(),
                    Forms\Components\Select::make('product_id')
                        ->label('Produk')
                        ->options(function () {
                            return Product::where('stock', '>', 0)
                                ->get()
                                ->mapWithKeys(fn ($product) => [
                                    $product->id => "{$product->name} (Sisa: {$product->stock})"
                                ]);
                        })
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
                    Forms\Components\Select::make('payment_method_id')
                        ->label('Metode Pembayaran')
                        ->options(PaymentMethod::all()->pluck('paymentmethods', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('total_price')->numeric()->required()->disabled(),
                ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')->label('Pelanggan')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('product.name')->searchable()->label('Produk'),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->label('Jumlah')
                    ->badge()
                    ->colors(['danger']),
                Tables\Columns\TextColumn::make('paymentMethod.paymentmethods')->searchable()->label('Metode Pembayaran')->badge(),
                Tables\Columns\TextColumn::make('total_price')->label('Total Harga')->searchable()->money('IDR'),
            ])
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateIcon('heroicon-o-archive-box-x-mark')
            ->emptyStateDescription('Tambahkan pesanan Terlebih Dahulu')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions'),
            ])
            ->filters([
                Filter::make('Tanggal Transaksi')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])            
            ->bulkActions([
                ExportBulkAction::make()
                    ->label('Export Transaksi')
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return Order::count() === 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

