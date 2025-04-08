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
    protected static ?string $navigationGroup = 'Products'; 
    protected static ?string $model = Product::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('price')->required()->numeric()
                ->minValue(0),
                TextInput::make('stock')->required()->numeric(),
                Select::make('discount_code')
                    ->label('Kode Voucher')
                    ->options(Discount::where('quota', '>', 0)->pluck('code', 'code')->toArray())
                    ->searchable()
                    ->nullable(),
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
                Tables\Columns\TextColumn::make('discount.code')->label('Kode Voucher')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->sortable()
                    ->getStateUsing(fn($record) => 'Rp ' . number_format($record->price, 2, ',', '.')),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions'),
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
    
    protected static function handleDiscountUsage(array $data): array
    {
        if (!empty($data['discount_code'])) {
            $discount = Discount::where('code', $data['discount_code'])->first();

            if (!$discount || $discount->quota <= 0) { 
                throw new \Exception('Kode diskon tidak valid atau sudah habis.');
            }

            $discount->quota -= 1;
            $discount->save();

            $data['discounted_price'] = $data['price'] - ($discount->amount ?? 0); 
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
