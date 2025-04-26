<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Actions\ActionGroup;

class ProductResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'DATA MASTER';
    protected static ?string $model = Product::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('price')->required()->numeric()->minValue(0),
                TextInput::make('stock')->required()->numeric(),
                Select::make('discount_code')
                    ->label('Diskon')
                    ->options(function () {
                        $discounts = Discount::valid()
                            ->where('end_date', '>=', now())
                            ->get();
                        
                        $options = [];
                        foreach ($discounts as $discount) {
                            $typeLabel = match ($discount->type) {
                                'percentage' => 'Persen',
                                default => $discount->type,
                            };
                            
                            $valueInfo = $discount->type === 'percentage' 
                                ? "{$discount->value}%" 
                                : "Rp " . number_format($discount->value, 0, ',', '.');
                                
                            $options[$discount->code] = "{$discount->code} - {$typeLabel} ({$valueInfo})";
                        }
                        
                        return $options;
                    })
                    ->nullable()
                    ->searchable(),
                    
                Forms\Components\FileUpload::make('images')
                    ->label('Images')
                    ->directory('images/gallery')
                    ->image()
                    ->required()
                    ->columnSpan('full')
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                    ),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')->label('Images'),
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori'),
                Tables\Columns\TextColumn::make('stock')->label('Stok')->badge(),

                TextColumn::make('discount_code')->label('Kode Voucher')->sortable()->searchable(),
                TextColumn::make('discount.value')
                    ->label('Nilai Diskon')
                    ->getStateUsing(fn ($record) =>
                        $record->discount
                            ? ($record->discount->type === 'percentage'
                                ? $record->discount->value . '%'
                                : 'Rp ' . number_format($record->discount->value, 2, ',', '.'))
                            : '-'
                    ),
                TextColumn::make('price')
                    ->label('Harga Asli')
                    ->getStateUsing(fn($record) => 'Rp ' . number_format($record->price, 2, ',', '.')),

                TextColumn::make('discounted_price')
                    ->label('Harga Setelah Diskon')
                    ->getStateUsing(fn($record) => 'Rp ' . number_format($record->discounted_price, 2, ',', '.')),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return self::handleDiscountUsage($data);
    }

    public static function mutateFormDataBeforeUpdate(array $data, Product $record): array
    {
        return self::handleDiscountUsage($data, $record);
    }

    public static function handleDiscountUsage(array $data, Product $record = null): array
    {
        if (isset($data['discount_code'])) {
            $discount = Discount::where('code', $data['discount_code'])->first();
            if ($discount && $discount->end_date < now()) {
                $data['discount_code'] = null;
                $discount->delete();
            }
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
