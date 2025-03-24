<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;

class DiscountResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $model = Discount::class;
    protected static ?string $navigationGroup = 'Management'; 
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('type')
                ->options([
                    'percentage' => 'Persentase (%)',
                    'fixed' => 'Potongan Langsung',
                    'buy1get1' => 'Buy 1 Get 1',
                    'voucher' => 'Voucher',
                ])
                ->required()
                ->reactive(),

            TextInput::make('value')
                ->label('Nilai Diskon')
                ->numeric()
                ->nullable()
                ->hidden(fn($get) => $get('type') === 'buy1get1')
                ->maxValue(100)
                ->required()
                ->rule('max:100', 'Nilai diskon tidak boleh melebihi 100%'),

            TextInput::make('quota')
                ->label('Kuota')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required(),

            DatePicker::make('expires_at')
                ->label('Tanggal Kedaluwarsa')
                ->displayFormat('d-m-Y')
                ->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode Diskon')->sortable()->searchable(),
                TextColumn::make('type')->label('Tipe Diskon'),
                TextColumn::make('value')->label('Nilai Diskon')->sortable(),
                TextColumn::make('quota')->label('Kuota')->sortable()->badge(),
                TextColumn::make('expires_at')
                    ->label('Kedaluwarsa')
                    ->date('d-m-Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
        ];
    }

    protected static function handleDiscountUsage(array $data): array
    {
        if (!empty($data['discount_code'])) {
            $discount = Discount::where('code', $data['discount_code'])->first();
    
            if (!$discount || !$discount->isValid()) {
                throw new \Exception('Kode diskon tidak valid atau sudah habis.');
            }
            $discount->decrement('quota', 1);
        }
        return $data;
    }
}